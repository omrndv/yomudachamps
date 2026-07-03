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
        
        // Cek jika QRIS tidak valid
        if (strlen($staticQris) < 10 || substr($staticQris, -8, 4) !== '6304') {
            return $staticQris;
        }

        // Hapus CRC lama
        $qrisTanpaCrc = substr($staticQris, 0, -4);

        // Ubah Point of Initiation Method menjadi 12 (Dinamis)
        $qrisTanpaCrc = str_replace('010211', '010212', $qrisTanpaCrc);

        // Pecah berdasarkan Tag 58 (Country Code ID)
        $pecah = explode('5802ID', $qrisTanpaCrc);

        if (count($pecah) < 2) {
            // Jika tidak ada 5802ID, fallback (walau standar QRIS pasti ada)
            return $staticQris;
        }

        // Sisipkan Tag 54 (Transaction Amount)
        $amountStr = (string)$amount;
        $amountTag = '54' . str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT) . $amountStr;

        // Gabungkan kembali
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
