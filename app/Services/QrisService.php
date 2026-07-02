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
            return $staticQris;
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

        // Tetap pertahankan Tag 51 (GPN) karena bank lain membutuhkannya untuk routing dasar
        // Tag 51 tidak boleh dihapus.

        // 1. Ubah Point of Initiation Method menjadi '12' (Dynamic QR)
        $tags['01'] = '12';

        // 2. Set nominal di tag 54
        $tags['54'] = (string) $amount;

        // 3. Set Convenience Fee Indicator di tag 55 menjadi '01' (Prompted to enter tip / No fee)
        $tags['55'] = '01';

        // 4. Set Convenience Fee Fixed (Tag 56) menjadi '000000000000' (Rp 0)
        $tags['56'] = '000000000000';

        // 5. Set Convenience Fee Percentage (Tag 57) menjadi '0000' (0.00%)
        $tags['57'] = '0000';

        // 6. Pastikan Tag 53 (Currency) adalah '360' (IDR)
        $tags['53'] = '360';

        // 7. Pastikan Tag 58 (Country Code) adalah 'ID' (Indonesia)
        $tags['58'] = 'ID';

        // 8. Susun / Update Tag 62 (Additional Data Field) secara lengkap
        // Kita isi sub-tag 01 (Bill Number), sub-tag 05 (Customer Label), sub-tag 06 (Reference Label), dan sub-tag 07 (Terminal ID)
        $billNo = "TRX" . str_pad($amount, 8, '0', STR_PAD_LEFT);
        $custLabel = "YOMUDAPAY";
        $refLabel = "YMD" . $amount;
        $termLabel = "A01"; // Terminal ID asli bawaan merchant Anda
        
        $subTag01 = "01" . str_pad(strlen($billNo), 2, '0', STR_PAD_LEFT) . $billNo;
        $subTag05 = "05" . str_pad(strlen($custLabel), 2, '0', STR_PAD_LEFT) . $custLabel;
        $subTag06 = "06" . str_pad(strlen($refLabel), 2, '0', STR_PAD_LEFT) . $refLabel;
        $subTag07 = "07" . str_pad(strlen($termLabel), 2, '0', STR_PAD_LEFT) . $termLabel;
        
        $tags['62'] = $subTag01 . $subTag05 . $subTag06 . $subTag07;

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
