<?php

namespace App\Http\Controllers\Qris;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\QrisTransaction;
use App\Services\QrisService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class QrisController extends Controller
{
    /**
     * Menampilkan halaman pembayaran QRIS Dinamis ke user
     */
    public function showPayment($trx_id)
    {
        $team = Team::with('season')->where('trx_id', $trx_id)->first();
        if (!$team && str_starts_with($trx_id, 'QUICK-')) {
            return redirect()->route('payment.detail', $trx_id);
        }

        if (!$team) {
            abort(404);
        }

        // Jika tim sudah berstatus PAID, langsung arahkan ke halaman sukses
        if ($team->status === 'PAID') {
            return redirect()->route('payment.success', $team->trx_id);
        }

        // Cari transaksi QRIS PENDING yang masih valid (belum expired)
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)
            ->where('status', 'PENDING')
            ->where('expires_at', '>', now())
            ->first();

        // Jika tidak ada, buat baru
        if (!$qrisTx) {
            $baseAmount = (int) $team->amount;
            if ($baseAmount <= 0) {
                $baseAmount = (int) ($team->season->price ?? 0);
            }
            $staticQris = Setting::getVal('gopay_static_qris');

            if (empty($staticQris)) {
                return back()->with('error', 'QRIS Statis belum dikonfigurasi di dashboard admin QRIS.');
            }

            $minCode = (int) Setting::getVal('manual_unique_min', 200);
            $maxCode = (int) Setting::getVal('manual_unique_max', 300);
            $adminFee = (int) Setting::getVal('manual_admin_fee', 0);
            $baseAmount = $baseAmount + $adminFee;

            $uniqueCode = 0;
            $finalAmount = $baseAmount;
            $found = false;

            if ($minCode === 0 && $maxCode === 0) {
                $found = true;
            } else {
                $maxAttempts = 1000;
                $attempt = 0;

                while ($attempt < $maxAttempts) {
                    $uniqueCode = rand($minCode, $maxCode);
                    $finalAmount = $baseAmount + $uniqueCode;

                    // Periksa apakah nominal final ini sedang digunakan oleh transaksi PENDING lain yang masih aktif
                    $exists = QrisTransaction::where('amount', $finalAmount)
                        ->where('status', 'PENDING')
                        ->where('expires_at', '>', now())
                        ->exists();

                    if (!$exists) {
                        $found = true;
                        break;
                    }
                    $attempt++;
                }
            }

            if (!$found) {
                return back()->with('error', 'Antrean nominal unik sedang penuh. Silakan coba sesaat lagi.');
            }

            try {
                // Expire any existing pending/claimed transactions for this team to prevent duplicates
                QrisTransaction::where('trx_id', $team->trx_id)
                    ->whereIn('status', ['PENDING', 'CLAIMED'])
                    ->update(['status' => 'EXPIRED']);

                // Jika staticQris adalah file image (starts with /uploads atau http)
                if (Str::startsWith($staticQris, ['/uploads', '/storage', 'http', 'https'])) {
                    $dynamicQrisString = $staticQris;
                } else {
                    $dynamicQrisString = QrisService::generateDynamicQris($staticQris, $finalAmount);
                }

                $qrisTx = QrisTransaction::create([
                    'id' => (string) Str::uuid(),
                    'trx_id' => $team->trx_id,
                    'base_amount' => $baseAmount,
                    'unique_code' => $uniqueCode,
                    'amount' => $finalAmount,
                    'qris_string' => $dynamicQrisString,
                    'status' => 'PENDING',
                    'expires_at' => now()->addMinutes(30), // Masa aktif 30 menit
                ]);

                // Catat notifikasi pembuatan transaksi baru
                \App\Models\GatewayNotification::add(
                    'TRANSACTION_CREATED',
                    'Transaksi Baru Dibuat',
                    "Pembayaran pendaftaran baru untuk Tim {$team->name} dibuat dengan nominal Rp " . number_format($finalAmount, 0, ',', '.') . " (Kode Unik: {$uniqueCode})."
                );
            } catch (Exception $e) {
                return back()->with('error', 'Gagal generate QRIS: ' . $e->getMessage());
            }
        }

        return redirect()->route('payment.detail', $team->trx_id);
    }

    /**
     * Endpoint API untuk pengecekan status transaksi via AJAX dari browser user
     */
    public function checkStatus($trx_id)
    {
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->latest()->first();

        if (!$qrisTx) {
            return response()->json(['status' => 'NOT_FOUND']);
        }

        // Jika status di database masih PENDING, namun waktu expired sudah lewat,
        // ubah statusnya menjadi EXPIRED
        if ($qrisTx->status === 'PENDING' && $qrisTx->expires_at->isPast()) {
            $qrisTx->update(['status' => 'EXPIRED']);
        }

        return response()->json([
            'status' => $qrisTx->status,
            'redirect_url' => $qrisTx->status === 'PAID' ? route('payment.success', $trx_id) : null
        ]);
    }

    /**
     * Memaksa pengecekan langsung (manual check) via API GoBiz
     */
    public function forceCheckStatus($trx_id)
    {
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->where('status', 'PENDING')->latest()->first();

        if (!$qrisTx) {
            $qrisTx = QrisTransaction::where('trx_id', $trx_id)->latest()->first();
            return response()->json([
                'status' => $qrisTx ? $qrisTx->status : 'NOT_FOUND',
                'redirect_url' => ($qrisTx && $qrisTx->status === 'PAID') ? route('payment.success', $trx_id) : null
            ]);
        }

        // Batasi request paksa (force check) maksimal sekali setiap 8 detik per transaksi demi keamanan API
        $throttleKey = 'qris_force_check_throttle_' . $trx_id;
        if (\Illuminate\Support\Facades\Cache::has($throttleKey)) {
            return response()->json([
                'status' => $qrisTx->status,
                'redirect_url' => $qrisTx->status === 'PAID' ? route('payment.success', $trx_id) : null
            ]);
        }
        
        \Illuminate\Support\Facades\Cache::put($throttleKey, true, 8);

        try {
            // Ambil 20 transaksi terakhir dari GoBiz
            $mutations = QrisService::fetchGoPayMutations();

            if (!empty($mutations)) {
                foreach ($mutations as $mutation) {
                    $rawAmount = $mutation['gross_amount'] ?? $mutation['amount'] ?? 0;
                    $mutationAmount = (int) ($rawAmount / 100);
                    $mutationStatus = strtoupper($mutation['transaction_status'] ?? $mutation['status'] ?? '');
                    
                    // Match amount and status
                    if ($mutationAmount == $qrisTx->amount && in_array($mutationStatus, ['SETTLEMENT', 'CAPTURE', 'PAID', 'SUCCESS'])) {
                        
                        $gopayRef = $mutation['wallstreet_transaction_id']
                                  ?? $mutation['acquiring_reference_number'] 
                                  ?? $mutation['acquirer_reference_number'] 
                                  ?? $mutation['reference_number'] 
                                  ?? $mutation['payment_reference'] 
                                  ?? $mutation['partner_payment_reference'] 
                                  ?? $mutation['rrn'] 
                                  ?? $mutation['transaction_id'] 
                                  ?? $mutation['id'] 
                                  ?? null;
                        
                        // Cek duplicate ref
                        $isDuplicate = false;
                        if ($gopayRef) {
                            $isDuplicate = QrisTransaction::where('gopay_reference', $gopayRef)->exists();
                        }
                        
                        if (!$isDuplicate) {
                            QrisService::settle($qrisTx, $gopayRef);
                            break;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Abaikan error jaringan saat force check, biarkan cron/polling yang urus nanti
        }

        return response()->json([
            'status' => $qrisTx->status,
            'redirect_url' => $qrisTx->status === 'PAID' ? route('payment.success', $trx_id) : null
        ])->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Debugging transaksi untuk mendiagnosis nominal 0 / salah
     */
    public function debugTrans($trx_id)
    {
        $team = Team::with('season')->where('trx_id', $trx_id)->first();
        if (!$team) {
            return response()->json(['error' => 'Team not found']);
        }
        return response()->json([
            'team_name' => $team->name,
            'team_amount' => $team->amount,
            'season_price' => $team->season->price ?? 'No Season',
            'qris_tx_details' => QrisTransaction::where('trx_id', $trx_id)->get()
        ]);
    }

    /**
     * Endpoint API publik untuk mengambil rincian checkout (CORS Enabled)
     */
    public function checkoutDetails($trx_id)
    {
        $team = Team::with('season')->where('trx_id', $trx_id)->first();
        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->latest()->first();

        if (!$qrisTx) {
            if ($team) {
                // Generate QRIS jika belum ada
                try {
                    $baseAmount = (int) $team->amount;
                    if ($baseAmount <= 0) {
                        $baseAmount = (int) ($team->season->price ?? 0);
                    }
                    $staticQris = Setting::getVal('gopay_static_qris');
                    if (!empty($staticQris)) {
                        $minCode = (int) Setting::getVal('manual_unique_min', 200);
                        $maxCode = (int) Setting::getVal('manual_unique_max', 300);
                        $adminFee = (int) Setting::getVal('manual_admin_fee', 0);
                        $baseAmount = $baseAmount + $adminFee;
                        
                        if ($minCode === 0 && $maxCode === 0) {
                            $uniqueCode = 0;
                        } else {
                            $uniqueCode = rand($minCode, $maxCode);
                        }
                        $finalAmount = $baseAmount + $uniqueCode;
                        
                        $dynamicQrisString = QrisService::generateDynamicQris($staticQris, $finalAmount);
                        
                        // Expire any existing pending/claimed transactions for this team to prevent duplicates
                        QrisTransaction::where('trx_id', $team->trx_id)
                            ->whereIn('status', ['PENDING', 'CLAIMED'])
                            ->update(['status' => 'EXPIRED']);

                        $qrisTx = QrisTransaction::create([
                            'id' => (string) Str::uuid(),
                            'trx_id' => $team->trx_id,
                            'base_amount' => $baseAmount,
                            'unique_code' => $uniqueCode,
                            'amount' => $finalAmount,
                            'qris_string' => $dynamicQrisString,
                            'status' => 'PENDING',
                            'expires_at' => now()->addMinutes(30),
                        ]);
                    }
                } catch (Exception $e) {
                    // Ignore errors
                }
            }
        }

        if (!$qrisTx) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404)->header('Access-Control-Allow-Origin', '*');
        }

        $secondsLeft = max(0, Carbon::parse($qrisTx->expires_at)->diffInSeconds(now(), false));

        return response()->json([
            'success'      => true,
            'trx_id'       => $trx_id,
            'team_name'    => $team ? $team->name : 'Quick Checkout',
            'season_name'  => ($team && $team->season) ? $team->season->name : 'Merchandise & Lainnya',
            'amount'       => $qrisTx->amount,
            'qris_string'  => $qrisTx->qris_string,
            'status'       => $qrisTx->status,
            'seconds_left' => $secondsLeft,
            'expires_at'   => $qrisTx->expires_at->toIso8601String(),
        ])->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Mengunggah bukti transfer manual
     */
    public function uploadProof(Request $request, $trx_id)
    {
        $request->validate([
            'proof_file' => 'required|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        $team = \App\Models\Team::where('trx_id', $trx_id)->first();
        if ($team && $team->status === 'PAID') {
            return back()->with('error', 'Pembayaran untuk tim ini sudah lunas / terverifikasi.');
        }

        $qrisTx = QrisTransaction::where('trx_id', $trx_id)->latest()->firstOrFail();

        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $filename = 'proof_' . $trx_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/proofs'), $filename);

            $qrisTx->update([
                'status' => 'CLAIMED',
                'gopay_reference' => 'PROOFS/' . $filename
            ]);

            return back()->with('success', 'Bukti transfer berhasil diunggah! Admin akan segera memverifikasi pembayaran Anda.');
        }

        return back()->with('error', 'Gagal mengunggah file bukti transfer.');
    }
}
