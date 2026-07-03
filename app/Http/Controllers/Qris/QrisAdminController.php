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

        $recentTransactions = QrisTransaction::with('team.season')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('qris.dashboard', compact('globalStats', 'monthlyLabels', 'monthlyCounts', 'weeklyCounts', 'recentTransactions'));
    }

    public function transactions()
    {
        $transactions = QrisTransaction::with('team.season')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('qris.transactions', compact('transactions'));
    }

    public function settings()
    {
        $config = (object) [
            'static_qris' => Setting::getVal('gopay_static_qris', ''),
            'merchant_id' => Setting::getVal('gopay_merchant_id', ''),
            'api_url' => Setting::getVal('gopay_api_url', 'https://api.gobiz.co.id/v2/transactions'),
            'has_token' => !empty(Setting::getVal('gopay_token', ''))
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

            $basePath = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? 'api.gojekapi.com') . ($parsedUrl['path'] ?? '');

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->timeout(15)->get($basePath, $queryParams);

            return response()->json([
                'status_code' => $response->status(),
                'is_successful' => $response->successful(),
                'raw_body' => $response->json() ?? $response->body()
            ]);
        } catch (Exception $e) {
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
                    $adminEmail = 'monotp94@gmail.com';
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

        // Update status transaksi QRIS
        $qrisTx->update([
            'status' => 'PAID',
            'paid_at' => now(),
            'gopay_reference' => 'MANUAL_SETTLE_' . strtoupper(Str::random(10))
        ]);

        return back()->with('success', "Transaksi untuk tim {$team->name} berhasil diselesaikan secara manual!");
    }

    /**
     * Menghapus transaksi QRIS dari database
     */
    public function deleteTransaction($trx_id)
    {
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->firstOrFail();
        $qrisTx->delete();
        return back()->with('success', "Transaksi dengan ID {$trx_id} berhasil dihapus!");
    }
}
