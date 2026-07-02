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

        // Standard QRIS EMVCo selalu diakhiri dengan tag 6304 dan 4 digit CRC (total 8 karakter terakhir)
        if (substr($staticQris, -8, 4) !== '6304') {
            throw new Exception("Format QRIS Statis tidak valid (tidak diakhiri dengan tag 6304)");
        }

        // Potong 8 karakter terakhir (6304XXXX)
        $qrisWithoutCrc = substr($staticQris, 0, -8);

        // Jalankan parser tag-by-tag untuk membaca struktur EMVCo asli
        $len = strlen($qrisWithoutCrc);
        $i = 0;
        $tags = [];
        
        while ($i < $len) {
            $tag = substr($qrisWithoutCrc, $i, 2);
            $length = (int) substr($qrisWithoutCrc, $i + 2, 2);
            $value = substr($qrisWithoutCrc, $i + 4, $length);
            
            if (empty($tag) || $length <= 0) {
                break;
            }
            
            $tags[$tag] = $value;
            $i += 4 + $length;
        }

        // Set / update nilai nominal di tag 54
        $tags['54'] = (string) $amount;

        // Susun kembali string QRIS dengan urutan tag teratur (ksort)
        ksort($tags);

        $qrisModified = '';
        foreach ($tags as $tag => $value) {
            $valLength = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
            $qrisModified .= $tag . $valLength . $value;
        }

        // Tempelkan tag 6304 untuk CRC
        $qrisModified .= '6304';

        // Hitung CRC16 baru
        $newCrc = self::crc16($qrisModified);

        return $qrisModified . $newCrc;
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

            // Bangun kembali URL dasar tanpa query string
            $basePath = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? 'api.gojekapi.com') . ($parsedUrl['path'] ?? '');

            // Panggil API GoPay Merchant
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->timeout(15)->get($basePath, $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                
                // Menyesuaikan struktur data response GoPay/GoFood Merchant
                return $data['transactions'] ?? $data['data'] ?? $data ?? [];
            }

            Log::error("GoPay Merchant API Error: Status {$response->status()} - {$response->body()}");
        } catch (Exception $e) {
            Log::error("Koneksi ke GoPay Merchant gagal: " . $e->getMessage());
        }

        return [];
    }
}
