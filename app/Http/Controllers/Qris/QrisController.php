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
        $team = Team::with('season')->where('trx_id', $trx_id)->firstOrFail();

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

            // Cari kode unik (1-50) yang tidak sedang aktif (tidak berstatus PENDING)
            $uniqueCode = 0;
            $maxAttempts = 1000;
            $attempt = 0;
            $found = false;

            while ($attempt < $maxAttempts) {
                // Gunakan mode server-managed (nominal dasar + kode unik)
                $uniqueCode = rand(1, 50);
                $finalAmount = $baseAmount + $uniqueCode;

                // Periksa apakah nominal final ini sedang digunakan oleh transaksi PENDING lain
                $exists = QrisTransaction::where('amount', $finalAmount)
                    ->where('status', 'PENDING')
                    ->exists();

                if (!$exists) {
                    $found = true;
                    break;
                }
                $attempt++;
            }

            if (!$found) {
                return back()->with('error', 'Antrean nominal unik sedang penuh. Silakan coba sesaat lagi.');
            }

            try {
                // Generate string QRIS dinamis menggunakan parser EMVCo
                $dynamicQrisString = QrisService::generateDynamicQris($staticQris, $finalAmount);

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
                        
                        $gopayRef = $mutation['id'] ?? $mutation['transaction_id'] ?? null;
                        
                        // Cek duplicate ref
                        $isDuplicate = false;
                        if ($gopayRef) {
                            $isDuplicate = QrisTransaction::where('gopay_reference', $gopayRef)->exists();
                        }
                        
                        if (!$isDuplicate) {
                            // Settle the payment
                            $qrisTx->update([
                                'status' => 'PAID',
                                'gopay_reference' => $gopayRef
                            ]);
                            
                            $team = Team::where('trx_id', $qrisTx->trx_id)->first();
                            if ($team) {
                                $team->status = 'PAID';
                                $team->save();
                                
                                // Catat notifikasi pembayaran lunas
                                \App\Models\GatewayNotification::add(
                                    'TRANSACTION_PAID',
                                    'Pembayaran Terverifikasi',
                                    "Pembayaran Tim {$team->name} sebesar Rp " . number_format($qrisTx->amount, 0, ',', '.') . " (Ref ID GoPay: {$gopayRef}) berhasil terverifikasi otomatis."
                                );
                            }
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
     * Mengunggah bukti transfer manual
     */
    public function uploadProof(Request $request, $trx_id)
    {
        $request->validate([
            'proof_file' => 'required|image|mimes:jpeg,png,jpg|max:4096',
        ]);

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
