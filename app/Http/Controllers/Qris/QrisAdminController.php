<?php

namespace App\Http\Controllers\Qris;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\QrisTransaction;
use App\Models\Setting;
use App\Notifications\NewRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class QrisAdminController extends Controller
{
    /**
     * Menampilkan halaman Login Admin QRIS
     */
    public function showLogin()
    {
        if (session('qris_authenticated') === true) {
            return redirect()->route('qris.dashboard');
        }
        return view('qris.login');
    }

    /**
     * Memproses Login Admin QRIS
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $envUser = env('QRIS_ADMIN_USERNAME', 'admin');
        $envPass = env('QRIS_ADMIN_PASSWORD', 'admin123'); // Default fallback untuk testing

        if ($request->username === $envUser && $request->password === $envPass) {
            session(['qris_authenticated' => true]);
            return redirect()->route('qris.dashboard')->with('success', 'Selamat datang di Panel Admin QRIS!');
        }

        return back()->withErrors(['message' => 'Username atau password salah.']);
    }

    /**
     * Memproses Logout Admin QRIS
     */
    public function logout()
    {
        session()->forget('qris_authenticated');
        return redirect()->route('qris.login')->with('success', 'Berhasil logout.');
    }

    public function dashboard()
    {
        // Hitung total data secara global untuk seluruh database
        $globalStats = (object) [
            'total_volume' => QrisTransaction::where('status', 'PAID')->sum('amount'),
            'paid_count' => QrisTransaction::where('status', 'PAID')->count(),
            'pending_count' => QrisTransaction::where('status', 'PENDING')->count(),
            'expired_count' => QrisTransaction::where('status', 'EXPIRED')->count(),
        ];

        // Hitung data chart bulanan (6 bulan terakhir)
        $monthlyCounts = [];
        $monthlyLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $monthlyCounts[] = QrisTransaction::where('status', 'PAID')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Hitung data chart mingguan (Senin - Minggu minggu ini)
        $weeklyCounts = [];
        for ($d = 1; $d <= 7; $d++) {
            $date = now()->startOfWeek()->addDays($d - 1);
            $weeklyCounts[] = QrisTransaction::where('status', 'PAID')
                ->whereDate('created_at', $date)
                ->count();
        }

        // Hitung success rate (paid vs expired)
        $totalEnded = $globalStats->paid_count + $globalStats->expired_count;
        $successRate = $totalEnded > 0 ? round(($globalStats->paid_count / $totalEnded) * 100, 1) : 100.0;

        $recentTransactions = QrisTransaction::with('team.season')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('qris.dashboard', compact('globalStats', 'monthlyLabels', 'monthlyCounts', 'weeklyCounts', 'recentTransactions', 'successRate'));
    }

    public function transactions(Request $request)
    {
        $query = QrisTransaction::with('team.season');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('trx_id', 'like', "%{$search}%")
                  ->orWhere('gopay_reference', 'like', "%{$search}%")
                  ->orWhereHas('team', function($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('multi_only') && $request->multi_only == '1') {
            $query->whereExists(function ($q) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                  ->from('qris_transactions as qt2')
                  ->whereRaw('qt2.trx_id = qris_transactions.trx_id')
                  ->whereRaw('DATE(qt2.created_at) = DATE(qris_transactions.created_at)')
                  ->whereRaw('qt2.id <> qris_transactions.id');
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Cari mutasi GoPay tak teridentifikasi (Anomali / Double Pay / Expired QRIS)
        $anomalies = [];
        try {
            $mutations = \App\Services\QrisService::fetchGoPayMutations();
            if (!empty($mutations)) {
                $dbReferences = QrisTransaction::whereNotNull('gopay_reference')
                    ->pluck('gopay_reference')
                    ->toArray();

                foreach ($mutations as $m) {
                    $refId = $m['wallstreet_transaction_id']
                           ?? $m['acquiring_reference_number'] 
                           ?? $m['acquirer_reference_number'] 
                           ?? $m['reference_number'] 
                           ?? $m['payment_reference'] 
                           ?? $m['partner_payment_reference'] 
                           ?? $m['rrn'] 
                           ?? $m['transaction_id'] 
                           ?? $m['id'] 
                           ?? null;
                    $status = strtoupper($m['transaction_status'] ?? $m['status'] ?? '');

                    if ($refId && !in_array($refId, $dbReferences) && in_array($status, ['SETTLEMENT', 'CAPTURE', 'PAID', 'SUCCESS'])) {
                        $rawAmount = $m['gross_amount'] ?? $m['amount'] ?? 0;
                        $amount = (int) ($rawAmount / 100);

                        // Cari transaksi mencurigakan (suspects) dengan nominal yang sama yang belum PAID
                        $suspects = QrisTransaction::with('team.season')
                            ->where('amount', $amount)
                            ->where('status', '!=', 'PAID')
                            ->latest()
                            ->take(3)
                            ->get();

                        if ($suspects->isNotEmpty()) {
                            $anomalies[] = [
                                'ref_id' => $refId,
                                'amount' => $amount,
                                'time' => $m['created_at'] ?? $m['time'] ?? now(),
                                'suspects' => $suspects
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika koneksi/API GoPay bermasalah
        }

        return view('qris.transactions', compact('transactions', 'anomalies'));
    }

    public function settings()
    {
        $rawToken = Setting::getVal('gopay_token', '');
        $token = '';
        if (!empty($rawToken)) {
            try {
                $token = Crypt::decryptString($rawToken);
            } catch (\Exception $e) {
                $token = ''; // Jika gagal dekripsi
            }
        }

        $config = (object) [
            'static_qris' => Setting::getVal('gopay_static_qris', ''),
            'merchant_id' => Setting::getVal('gopay_merchant_id', ''),
            'api_url' => Setting::getVal('gopay_api_url', 'https://api.gobiz.co.id/v2/transactions'),
            'has_token' => !empty($rawToken),
            'token' => $token
        ];
        return view('qris.settings', compact('config'));
    }

    public function testPoll()
    {
        // Fungsi khusus untuk melihat respons asli dari GoPay API
        $apiUrl = Setting::getVal('gopay_api_url', 'https://api.gobiz.co.id/v2/transactions');
        $encryptedToken = Setting::getVal('gopay_token');
        
        if (!$encryptedToken) {
            return response()->json(['error' => 'Token GoBiz belum dikonfigurasi. Silakan isi token di pengaturan.']);
        }

        try {
            $token = Crypt::decryptString($encryptedToken);
            $parsedUrl = parse_url($apiUrl);
            $queryParams = [];
            if (isset($parsedUrl['query'])) parse_str($parsedUrl['query'], $queryParams);
            
            $queryParams['start_time'] = gmdate('Y-m-d\TH:i:s.000\Z', time() - 86400 * 2);
            $queryParams['end_time'] = gmdate('Y-m-d\TH:i:s.999\Z', time() + 86400);
            if (!isset($queryParams['size'])) $queryParams['size'] = 20;
            if (!isset($queryParams['from'])) $queryParams['from'] = 0;
            
            $merchantId = Setting::getVal('gopay_merchant_id');
            $queryParams['merchant_ids'] = $merchantId;

            $basePath = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? 'api.gojekapi.com') . ($parsedUrl['path'] ?? '');

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Authentication-Type' => 'go-id',
                'Accept' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Origin' => 'https://portal.gofoodmerchant.co.id',
                'Referer' => 'https://portal.gofoodmerchant.co.id/'
            ])->timeout(15)->get($basePath, $queryParams);

            if (!$response->successful()) {
                \App\Services\QrisService::notifyAdminTokenExpired("API Health Check Gagal: Status {$response->status()} - {$response->body()}");
            } else {
                // Catat log pemulihan API jika sebelumnya bermasalah
                $lastNotification = \App\Models\GatewayNotification::latest()->first();
                if ($lastNotification && $lastNotification->type === 'API_ERROR') {
                    \App\Models\GatewayNotification::add(
                        'API_SUCCESS',
                        'GoPay API Kembali Normal',
                        'Koneksi API GoPay Merchant berhasil terhubung kembali dan status sistem kembali Aktif/Sehat.'
                    );
                }
            }

            return response()->json([
                'status_code' => $response->status(),
                'is_successful' => $response->successful(),
                'raw_body' => $response->json() ?? $response->body()
            ]);
        } catch (Exception $e) {
            \App\Services\QrisService::notifyAdminTokenExpired("API Health Check Koneksi Gagal: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Memperbarui Konfigurasi API & QRIS GoPay Merchant
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'static_qris' => 'required|string',
            'merchant_id' => 'required|string',
            'api_url' => 'required|url',
            'token' => 'nullable|string',
        ]);

        // Simpan konfigurasi
        Setting::setVal('gopay_static_qris', trim($request->static_qris));
        Setting::setVal('gopay_merchant_id', trim($request->merchant_id));
        Setting::setVal('gopay_api_url', trim($request->api_url));

        if ($request->filled('token')) {
            // Enkripsi token sebelum disimpan ke database
            Setting::setVal('gopay_token', Crypt::encryptString($request->token));
        }

        return back()->with('success', 'Konfigurasi berhasil disimpan!');
    }

    /**
     * Penyelesaian Transaksi secara Manual (Manual Settle / Force Paid)
     */
    public function manualSettle($trx_id)
    {
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->firstOrFail();
        $team = Team::with('season')->where('trx_id', $trx_id)->firstOrFail();

        $statusLama = $team->status;
        $team->status_tripay = 'PAID';

        $currentPaidCount = Team::where('season_id', $team->season_id)
            ->where('status', 'PAID')
            ->count();

        if ($currentPaidCount < $team->season->slot) {
            $team->status = 'PAID';

            if ($statusLama !== 'PAID') {
                // Kirim notifikasi email ke admin
                try {
                    $adminEmail = \App\Models\Setting::getVal('admin_notification_email', 'monotp94@gmail.com');
                    Notification::route('mail', $adminEmail)->notify(new NewRegistration($team));
                } catch (Exception $e) {
                    Log::error('Gagal kirim email (manual settle): ' . $e->getMessage());
                }

                // Kirim WhatsApp otomatis ke perwakilan tim
                try {
                    \App\Services\WhatsappService::sendPaidNotification($team);
                } catch (Exception $e) {
                    Log::error('Gagal kirim WhatsApp otomatis (manual settle): ' . $e->getMessage());
                }
            }
        } else {
            $team->status = 'FAILED';
            Log::warning("OVER-SLOT: Tim {$team->name} diselesaikan manual tapi slot penuh.");

            // Kirim Notifikasi Over-slot
            try {
                \App\Services\WhatsappService::sendAdminOverSlotNotification($team);
                \App\Services\WhatsappService::sendUserOverSlotNotification($team);
            } catch (Exception $e) {
                Log::error('Gagal kirim notifikasi Over-Slot (manual settle): ' . $e->getMessage());
            }
        }

        $team->save();

        // Clear anomalies count cache
        \Illuminate\Support\Facades\Cache::forget('qris_anomalies_count');

        // Update status transaksi QRIS
        $gopayRef = request('gopay_ref') ?: 'MANUAL_SETTLE_' . strtoupper(Str::random(10));
        $qrisTx->update([
            'status' => 'PAID',
            'paid_at' => now(),
            'gopay_reference' => $gopayRef
        ]);

        return back()->with('success', "Transaksi untuk tim {$team->name} berhasil diselesaikan secara manual!");
    }

    /**
     * Mengubah status transaksi menjadi REFUNDED (Void)
     */
    public function refundTransaction($trx_id)
    {
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->firstOrFail();
        $qrisTx->update(['status' => 'REFUNDED']);
        
        $team = Team::where('trx_id', $trx_id)->first();
        if ($team) {
            $team->status = 'FAILED'; // Tandai gagal di sistem pendaftaran
            $team->save();
        }
        
        \Illuminate\Support\Facades\Cache::forget('qris_anomalies_count');
        return back()->with('success', "Transaksi dengan ID {$trx_id} berhasil ditandai sebagai REFUNDED!");
    }

    /**
     * Memperbarui masa aktif QRIS (Dynamic Expiry)
     */
    public function updateExpiry(Request $request, $trx_id)
    {
        $request->validate(['minutes' => 'required|integer|min:1']);
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->firstOrFail();
        
        $qrisTx->update([
            'expires_at' => now()->addMinutes($request->minutes),
            'status' => 'PENDING' // Kembalikan ke pending jika expired
        ]);
        
        return back()->with('success', "Masa aktif transaksi {$trx_id} berhasil diubah!");
    }

    /**
     * Menghapus transaksi QRIS dari database
     */
    public function deleteTransaction($trx_id)
    {
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->firstOrFail();
        $qrisTx->delete();
        \Illuminate\Support\Facades\Cache::forget('qris_anomalies_count');
        return back()->with('success', "Transaksi dengan ID {$trx_id} berhasil dihapus!");
    }

    /**
     * Sinkronisasi paksa semua transaksi pending dengan GoBiz
     */
    public function syncPending()
    {
        try {
            \Illuminate\Support\Facades\Cache::forget('qris_anomalies_count');
            \Illuminate\Support\Facades\Artisan::call('qris:poll');
            return back()->with('success', 'Berhasil melakukan sinkronisasi paksa (Sync Pending). Semua transaksi tertunda telah dicocokkan.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus transaksi terpilih secara massal
     */
    public function deleteBulkTransactions(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu transaksi untuk dihapus.');
        }

        QrisTransaction::whereIn('id', $ids)->delete();
        \Illuminate\Support\Facades\Cache::forget('qris_anomalies_count');

        return back()->with('success', count($ids) . ' transaksi berhasil dihapus sekaligus!');
    }

    /**
     * Menyelesaikan (Settle/PAID) transaksi terpilih secara massal
     */
    public function settleBulkTransactions(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu transaksi untuk diselesaikan.');
        }

        $transactions = QrisTransaction::with('team.season')->whereIn('id', $ids)
            ->whereIn('status', ['PENDING', 'EXPIRED', 'CLAIMED'])
            ->get();

        if ($transactions->isEmpty()) {
            return back()->with('error', 'Tidak ada transaksi yang bisa diselesaikan dari pilihan tersebut.');
        }

        $settledCount = 0;
        foreach ($transactions as $qrisTx) {
            $team = $qrisTx->team;
            if (!$team) continue;

            $currentPaidCount = Team::where('season_id', $team->season_id)->where('status', 'PAID')->count();
            if ($currentPaidCount < $team->season->slot) {
                $team->status = 'PAID';
                $team->status_tripay = 'PAID';
                $team->save();
            }

            $qrisTx->update([
                'status'         => 'PAID',
                'paid_at'        => now(),
                'gopay_reference'=> 'BULK_SETTLE_' . strtoupper(Str::random(8)),
            ]);
            $settledCount++;
        }

        return back()->with('success', "{$settledCount} transaksi berhasil diselesaikan sekaligus!");
    }

    /**
     * Halaman Rekonsiliasi GoPay vs Database (Side-by-side)
     */
    public function rekonsiliasi()
    {
        \Illuminate\Support\Facades\Cache::forget('gopay_mutations_api_cache');
        $mutations = \App\Services\QrisService::fetchGoPayMutations(50);

        $dbReferences = QrisTransaction::whereNotNull('gopay_reference')
            ->pluck('gopay_reference')
            ->toArray();

        $rows = [];
        foreach ($mutations as $m) {
            $refId = $m['wallstreet_transaction_id'] ?? $m['id'] ?? null;
            $rawAmount = $m['gross_amount'] ?? $m['amount'] ?? 0;
            $amount = (int)($rawAmount / 100);
            $status = strtoupper($m['transaction_status'] ?? $m['status'] ?? '');
            $isMatched = $refId && in_array($refId, $dbReferences);

            $dbTx = null;
            if ($isMatched) {
                $dbTx = QrisTransaction::with('team')->where('gopay_reference', $refId)->first();
            } else {
                // Coba cocokkan berdasarkan nominal
                $dbTx = QrisTransaction::with('team')
                    ->where('amount', $amount)
                    ->whereIn('status', ['PENDING', 'EXPIRED'])
                    ->latest()
                    ->first();
            }

            $rows[] = [
                'ref_id'     => $refId,
                'amount'     => $amount,
                'time'       => $m['transaction_time'] ?? $m['settlement_time'] ?? null,
                'issuer'     => $m['qris_provider_aspi_issuer'] ?? '-',
                'gopay_status' => $status,
                'is_matched' => $isMatched,
                'db_tx'      => $dbTx,
            ];
        }

        return view('qris.rekonsiliasi', compact('rows'));
    }

    /**
     * Halaman Laporan / Export
     */
    public function laporan(Request $request)
    {
        $query = QrisTransaction::with('team.season')->where('status', 'PAID');

        if ($request->filled('dari')) {
            $query->whereDate('paid_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('paid_at', '<=', $request->sampai);
        }
        if ($request->filled('season_id')) {
            $query->whereHas('team', fn($q) => $q->where('season_id', $request->season_id));
        }

        $transactions = $query->orderBy('paid_at', 'desc')->get();

        $totalVolume = $transactions->sum('amount');

        $seasons = \App\Models\Season::orderBy('name')->get();

        return view('qris.laporan', compact('transactions', 'totalVolume', 'seasons'));
    }

    /**
     * Export CSV Transaksi Sukses
     */
    public function exportCsv(Request $request)
    {
        $query = QrisTransaction::with('team.season')->where('status', 'PAID');

        if ($request->filled('dari'))    $query->whereDate('paid_at', '>=', $request->dari);
        if ($request->filled('sampai'))  $query->whereDate('paid_at', '<=', $request->sampai);
        if ($request->filled('season_id')) {
            $query->whereHas('team', fn($q) => $q->where('season_id', $request->season_id));
        }

        $transactions = $query->orderBy('paid_at', 'desc')->get();

        $filename = 'laporan-qris-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $out = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['No', 'ID Transaksi', 'No. Ref GoPay', 'Nama Tim', 'Season', 'Nominal', 'Kode Unik', 'Waktu Bayar']);
            foreach ($transactions as $i => $tx) {
                fputcsv($out, [
                    $i + 1,
                    $tx->trx_id,
                    $tx->gopay_reference ?? '-',
                    $tx->team->name ?? 'Tim Terhapus',
                    $tx->team->season->name ?? '-',
                    $tx->amount,
                    $tx->unique_code ?? 0,
                    $tx->paid_at ? $tx->paid_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Halaman detail riwayat transaksi satu tim
     */
    public function teamDetail($team_id)
    {
        $team = Team::with('season')->findOrFail($team_id);
        $transactions = QrisTransaction::where('trx_id', $team->trx_id ?? '')
            ->orWhere(fn($q) => $q->whereHas('team', fn($t) => $t->where('id', $team_id)))
            ->orderBy('created_at', 'desc')
            ->get();

        // Coba ambil via trx_id team
        if ($transactions->isEmpty() && $team->trx_id) {
            $transactions = QrisTransaction::where('trx_id', $team->trx_id)->get();
        }

        return view('qris.team-detail', compact('team', 'transactions'));
    }

    /**
     * Ganti password login Gateway
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $currentEnvPass = env('QRIS_ADMIN_PASSWORD', 'admin123');
        if ($request->current_password !== $currentEnvPass) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // Simpan password baru di settings DB (sebagai override env)
        Setting::setVal('qris_admin_password_override', $request->new_password);
        // Juga update file .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        if (str_contains($envContent, 'QRIS_ADMIN_PASSWORD=')) {
            $envContent = preg_replace('/QRIS_ADMIN_PASSWORD=.*/', 'QRIS_ADMIN_PASSWORD=' . $request->new_password, $envContent);
        } else {
            $envContent .= "\nQRIS_ADMIN_PASSWORD=" . $request->new_password;
        }
        file_put_contents($envPath, $envContent);

        return back()->with('success', 'Password Gateway berhasil diubah!');
    }

    /**
     * Export Laporan PDF (Keuangan Resmi)
     */
    public function exportPdf(Request $request)
    {
        $query = QrisTransaction::with('team.season')->where('status', 'PAID');

        if ($request->filled('dari'))    $query->whereDate('paid_at', '>=', $request->dari);
        if ($request->filled('sampai'))  $query->whereDate('paid_at', '<=', $request->sampai);
        if ($request->filled('season_id')) {
            $query->whereHas('team', fn($q) => $q->where('season_id', $request->season_id));
        }

        $transactions = $query->orderBy('paid_at', 'desc')->get();
        $totalVolume = $transactions->sum('amount');

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        
        // Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'YOMUDA CHAMPS - LAPORAN KEUANGAN QRIS', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, 'Dihasilkan pada: ' . now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB', 0, 1, 'C');
        $pdf->Ln(10);

        // Filter info
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Periode:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $dari = $request->input('dari', 'Semua');
        $sampai = $request->input('sampai', 'Semua');
        $pdf->Cell(80, 6, $dari . ' s/d ' . $sampai, 0, 1);
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Total Transaksi:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(80, 6, $transactions->count() . ' Berhasil', 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Total Akumulasi:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(80, 6, 'Rp ' . number_format($totalVolume, 0, ',', '.'), 0, 1);
        $pdf->Ln(8);

        // Table Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'ID Transaksi', 1, 0, 'L', true);
        $pdf->Cell(65, 8, 'Nama Tim', 1, 0, 'L', true);
        $pdf->Cell(35, 8, 'Nominal', 1, 0, 'R', true);
        $pdf->Cell(35, 8, 'Waktu Bayar', 1, 1, 'C', true);

        // Table Rows
        $pdf->SetFont('Arial', '', 9);
        foreach ($transactions as $i => $tx) {
            $pdf->Cell(10, 7, $i + 1, 1, 0, 'C');
            $pdf->Cell(45, 7, $tx->trx_id, 1, 0, 'L');
            $pdf->Cell(65, 7, substr($tx->team->name ?? 'Tim Terhapus', 0, 30), 1, 0, 'L');
            $pdf->Cell(35, 7, 'Rp ' . number_format($tx->amount, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(35, 7, $tx->paid_at ? $tx->paid_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-', 1, 1, 'C');
        }

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-qris-' . now()->format('Y-m-d') . '.pdf"'
        ]);
    }

    /**
     * Unduh Invoice PDF Resmi untuk Tim
     */
    public function downloadInvoice($trx_id)
    {
        $qrisTx = QrisTransaction::with('team.season')->where('trx_id', $trx_id)->firstOrFail();
        $team = $qrisTx->team;

        $pdf = new \FPDF('P', 'mm', array(100, 150));
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'YOMUDA CHAMPS', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 4, 'BUKTI PEMBAYARAN SAH (INVOICE)', 0, 1, 'C');
        $pdf->Cell(0, 4, '====================================', 0, 1, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, 'ID Transaksi:', 0, 0);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, $qrisTx->trx_id, 0, 1);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, 'No. Ref GoPay:', 0, 0);
        $pdf->Cell(0, 5, $qrisTx->gopay_reference ?? '-', 0, 1);

        $pdf->Cell(30, 5, 'Nama Tim:', 0, 0);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0, 5, $team->name ?? 'Tim Terhapus', 0, 1);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(30, 5, 'Season:', 0, 0);
        $pdf->Cell(0, 5, $team->season->name ?? '-', 0, 1);

        $pdf->Cell(30, 5, 'Waktu Bayar:', 0, 0);
        $pdf->Cell(0, 5, $qrisTx->paid_at ? $qrisTx->paid_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : '-', 0, 1);

        $pdf->Cell(0, 4, '------------------------------------', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 6, 'TOTAL BAYAR:', 0, 0);
        $pdf->Cell(0, 6, 'Rp ' . number_format($qrisTx->amount, 0, ',', '.'), 0, 1);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(0, 8, 'STATUS: LUNAS / PAID', 0, 1, 'C');

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->Ln(6);
        $pdf->Cell(0, 4, 'Terima kasih atas partisipasi Anda.', 0, 1, 'C');
        $pdf->Cell(0, 4, 'Simpan invoice ini sebagai bukti pendaftaran.', 0, 1, 'C');

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-' . $trx_id . '.pdf"'
        ]);
    }

    /**
     * Endpoint sementara untuk mendump respon asli dari API GoPay Merchant
     */
    public function testGopayResponse()
    {
        // Bypass cache untuk mendapatkan respon fresh
        \Illuminate\Support\Facades\Cache::forget('gopay_mutations_api_cache');
        
        $mutations = \App\Services\QrisService::fetchGoPayMutations();

        $updatedCount = 0;
        foreach ($mutations as $m) {
            $uuid = $m['id'] ?? null;
            $wallstreetId = $m['wallstreet_transaction_id'] ?? null;

            if ($uuid && $wallstreetId) {
                // Update jika ada transaksi di DB yang masih memakai UUID
                $affected = QrisTransaction::where('gopay_reference', $uuid)
                    ->update(['gopay_reference' => $wallstreetId]);
                $updatedCount += $affected;
            }
        }
        
        return response()->json([
            'count' => count($mutations),
            'updated_db_records' => $updatedCount,
            'mutations' => $mutations
        ]);
    }
}

