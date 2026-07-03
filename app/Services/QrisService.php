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
    public static function fetchGoPayMutations(): array
    {
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
                $queryParams['size'] = 20;
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
                return $data['transactions'] ?? $data['data'] ?? $data ?? [];
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
    private static function notifyAdminTokenExpired(string $errorDetail)
    {
        try {
            $cacheKey = 'qris_token_expired_notified';
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $adminEmail = Setting::getVal('admin_notification_email', 'monotp94@gmail.com');
                \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
                    ->notify(new \App\Notifications\QrisTokenExpired($errorDetail));

                // Simpan cache flag selama 12 jam (43200 detik)
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, 43200);
                Log::info("Email notifikasi token expired telah dikirim ke {$adminEmail}");
            }
        } catch (Exception $e) {
            Log::error("Gagal mengirim email notifikasi token expired: " . $e->getMessage());
        }
    }
}
