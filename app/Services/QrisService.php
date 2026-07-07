<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Models\Setting;
use Exception;

class QrisService
{
    /**
     * Menghitung CRC16 checksum standar EMVCo
     */
    public static function crc16($data): string
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Mengubah QRIS Statis menjadi Dinamis dengan nominal tertentu (EMVCo Parser Aman)
     */
    public static function generateDynamicQris(string $staticQris, int $amount): string
    {
        $staticQris = trim($staticQris);
        
        if (strlen($staticQris) < 10 || substr($staticQris, -8, 4) !== '6304') {
            return $staticQris;
        }

        // Hapus CRC lama
        $qrisTanpaCrc = substr($staticQris, 0, -4);
        
        // Ubah Point of Initiation Method menjadi 12 (Dinamis / Nominal Terkunci)
        $qrisTanpaCrc = str_replace('010211', '010212', $qrisTanpaCrc);

        // Pecah berdasarkan Tag 58 (Country Code ID)
        $pecah = explode('5802ID', $qrisTanpaCrc);

        if (count($pecah) < 2) {
            return $staticQris;
        }

        // Sisipkan Tag 54 (Transaction Amount)
        $amountStr = (string)$amount;
        $amountTag = '54' . str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT) . $amountStr;

        // Gabungkan kembali (Tanpa Tag 55 Tip Indicator agar didukung oleh semua e-wallet/M-banking)
        $qrisBaru = $pecah[0] . $amountTag . '5802ID' . $pecah[1];

        // Hitung dan tempelkan CRC16 baru
        return $qrisBaru . self::crc16($qrisBaru);
    }

    /**
     * Mengambil daftar mutasi dari API GoPay Merchant
     */
    public static function fetchGoPayMutations(int $limit = 50): array
    {
        return []; // Akun GoBiz dinonaktifkan: Bypass API calls untuk menghindari loading lama
        $cacheKey = 'gopay_mutations_api_cache';
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return \Illuminate\Support\Facades\Cache::get($cacheKey) ?? [];
        }

        $apiUrl = Setting::getVal('gopay_api_url', 'https://api.gobiz.co.id/v2/transactions');
        $merchantId = Setting::getVal('gopay_merchant_id');
        $encryptedToken = Setting::getVal('gopay_token');

        if (!$encryptedToken || !$merchantId) {
            Log::warning("Poller QRIS ditangguhkan: Token GoPay atau Merchant ID belum dikonfigurasi.");
            return [];
        }

        try {
            $token = Crypt::decryptString($encryptedToken);
        } catch (Exception $e) {
            Log::error("Gagal mendeskripsi Token GoPay: " . $e->getMessage());
            return [];
        }

        try {
            // Parse URL dan query params dari konfigurasi user
            $parsedUrl = parse_url($apiUrl);
            $queryParams = [];
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
            }

            // Set start_time dan end_time secara dinamis agar API GoPay tidak Bad Request
            // Mengambil range transaksi dari 2 hari lalu hingga besok
            $queryParams['start_time'] = gmdate('Y-m-d\TH:i:s.000\Z', time() - 86400 * 2);
            $queryParams['end_time'] = gmdate('Y-m-d\TH:i:s.999\Z', time() + 86400);

            // Jika limit atau page belum ditentukan di URL, set defaultnya
            if (!isset($queryParams['size'])) {
                $queryParams['size'] = $limit;
            }
            if (!isset($queryParams['from'])) {
                $queryParams['from'] = 0;
            }

            // Tambahkan merchant_ids dari konfigurasi
            $queryParams['merchant_ids'] = $merchantId;

            // Bangun kembali URL dasar tanpa query string
            $basePath = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? 'api.gojekapi.com') . ($parsedUrl['path'] ?? '');

            // Panggil API GoPay Merchant
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Authentication-Type' => 'go-id',
                'Accept' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Origin' => 'https://portal.gofoodmerchant.co.id',
                'Referer' => 'https://portal.gofoodmerchant.co.id/'
            ])->timeout(15)->get($basePath, $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                
                // Menyesuaikan struktur data response GoPay/GoFood Merchant
                $transactions = $data['transactions'] ?? $data['data'] ?? $data ?? [];
                
                // Cache mutasi selama 3 detik untuk melindungi dari spam beberapa user sekaligus
                \Illuminate\Support\Facades\Cache::put($cacheKey, $transactions, 3);
                
                return $transactions;
            }

            // Deteksi token expired (biasanya 401 Unauthorized atau 403 Forbidden)
            if ($response->status() === 401 || $response->status() === 403) {
                self::notifyAdminTokenExpired("API Error: Status {$response->status()} - " . ($response->json()['message'] ?? $response->body()));
            }

            Log::error("GoPay Merchant API Error: Status {$response->status()} - {$response->body()}");
        } catch (Exception $e) {
            Log::error("Koneksi ke GoPay Merchant gagal: " . $e->getMessage());
            self::notifyAdminTokenExpired("Koneksi gagal: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Mengirim notifikasi email ke admin jika token expired (dengan throttling 12 jam)
     */
    public static function notifyAdminTokenExpired(string $errorDetail)
    {
        try {
            // Catat ke log notifikasi gateway dengan pencegahan banjir data (flood prevention) 5 menit
            $lastNotification = \App\Models\GatewayNotification::where('type', 'API_ERROR')
                ->where('created_at', '>=', now()->subMinutes(5))
                ->latest()
                ->first();

            if (!$lastNotification || !str_contains($lastNotification->message, substr($errorDetail, 0, 50))) {
                \App\Models\GatewayNotification::add('API_ERROR', 'GoPay API Bermasalah', $errorDetail);
            }

            $cacheKey = 'qris_token_expired_notified';
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $adminEmail = Setting::getVal('admin_notification_email', 'monotp94@gmail.com');
                \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                    ->notify(new \App\Notifications\QrisTokenExpired($errorDetail));

                // Simpan cache flag selama 2 jam (7200 detik)
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, 7200);
                Log::info("Email notifikasi token expired telah dikirim ke {$adminEmail}");
            }
        } catch (Exception $e) {
            Log::error("Gagal mengirim email notifikasi token expired: " . $e->getMessage());
        }
    }

    /**
     * Settle payment atomically using Cache Lock to prevent race conditions
     */
    public static function settle(\App\Models\QrisTransaction $qrisTx, string $gopayRef): bool
    {
        $lock = \Illuminate\Support\Facades\Cache::lock('qris_settle_lock_' . $qrisTx->id, 15);
        if (!$lock->get()) {
            Log::info("Settle ditangguhkan: transaksi {$qrisTx->id} sedang diproses oleh proses lain.");
            return false;
        }

        try {
            $qrisTx->refresh();
            if ($qrisTx->status === 'PAID') {
                return false;
            }

            $team = \App\Models\Team::with('season')->where('trx_id', $qrisTx->trx_id)->first();
            if (!$team) {
                $qrisTx->update([
                    'status' => 'PAID',
                    'paid_at' => now(),
                    'gopay_reference' => $gopayRef
                ]);
                return true;
            }

            $statusLama = $team->status;
            $team->status_tripay = 'PAID';

            $currentPaidCount = \App\Models\Team::where('season_id', $team->season_id)
                ->where('status', 'PAID')
                ->count();

            if ($currentPaidCount < $team->season->slot) {
                $team->status = 'PAID';

                // Catat notifikasi pembayaran lunas
                \App\Models\GatewayNotification::add(
                    'TRANSACTION_PAID',
                    'Pembayaran Terverifikasi',
                    "Pembayaran Tim {$team->name} sebesar Rp " . number_format($qrisTx->amount, 0, ',', '.') . " (Ref ID GoPay: {$gopayRef}) berhasil terverifikasi."
                );

                if ($statusLama !== 'PAID') {
                    // Kirim notifikasi email ke admin
                    try {
                        $adminEmail = \App\Models\Setting::getVal('admin_notification_email', 'monotp94@gmail.com');
                        \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)->notify(new \App\Notifications\NewRegistration($team));
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim email (settle): ' . $e->getMessage());
                    }

                    // Kirim WhatsApp otomatis ke perwakilan tim
                    try {
                        \App\Services\WhatsappService::sendPaidNotification($team);
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim WhatsApp otomatis (settle): ' . $e->getMessage());
                    }
                }
            } else {
                $team->status = 'FAILED';
                Log::warning("OVER-SLOT: Tim {$team->name} terdeteksi bayar tapi slot penuh.");

                // Catat notifikasi over-slot
                \App\Models\GatewayNotification::add(
                    'API_ERROR',
                    'Pembayaran Over-Slot Terdeteksi',
                    "Tim {$team->name} melakukan pembayaran sebesar Rp " . number_format($qrisTx->amount, 0, ',', '.') . " (Ref ID GoPay: {$gopayRef}), tetapi slot Season {$team->season->name} sudah penuh! Status tim ditandai FAILED."
                );

                // Kirim Notifikasi Over-slot
                try {
                    \App\Services\WhatsappService::sendAdminOverSlotNotification($team);
                    \App\Services\WhatsappService::sendUserOverSlotNotification($team);
                } catch (\Exception $e) {
                    Log::error('Gagal kirim notifikasi Over-Slot (settle): ' . $e->getMessage());
                }
            }

            $team->save();

            // Update status transaksi QRIS
            $qrisTx->update([
                'status' => 'PAID',
                'paid_at' => now(),
                'gopay_reference' => $gopayRef
            ]);

            return true;
        } finally {
            $lock->release();
        }
    }
}
