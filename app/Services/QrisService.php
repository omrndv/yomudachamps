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
     * Mengubah QRIS Statis menjadi Dinamis dengan nominal tertentu
     */
    public static function generateDynamicQris(string $staticQris, int $amount): string
    {
        // Bersihkan spasi
        $staticQris = trim($staticQris);

        // Standard QRIS EMVCo selalu diakhiri dengan tag 6304 dan 4 digit CRC (total 8 karakter terakhir)
        if (substr($staticQris, -8, 4) !== '6304') {
            throw new Exception("Format QRIS Statis tidak valid (tidak diakhiri dengan tag 6304)");
        }

        // Potong CRC bawaan
        $qrisWithoutCrc = substr($staticQris, 0, -4);

        // Cari tag 54 (Transaction Amount)
        // Format EMVCo: Tag (2 digit) + Length (2 digit) + Value
        $tag54Pos = strpos($qrisWithoutCrc, '54');
        $amountValue = (string) $amount;
        $amountLength = str_pad(strlen($amountValue), 2, '0', STR_PAD_LEFT);
        $newAmountTag = '54' . $amountLength . $amountValue;

        if ($tag54Pos !== false && $tag54Pos < strpos($qrisWithoutCrc, '5802ID')) {
            // Jika tag 54 sudah ada, ganti nilainya
            // Kita perlu membaca panjang nilai tag 54 lama untuk memotongnya dengan benar
            $oldLength = (int) substr($qrisWithoutCrc, $tag54Pos + 2, 2);
            $beforeTag54 = substr($qrisWithoutCrc, 0, $tag54Pos);
            $afterTag54 = substr($qrisWithoutCrc, $tag54Pos + 4 + $oldLength);
            
            $qrisModified = $beforeTag54 . $newAmountTag . $afterTag54;
        } else {
            // Jika belum ada, sisipkan sebelum tag 58 (Country Code "5802ID")
            $countryCodePos = strpos($qrisWithoutCrc, '5802ID');
            if ($countryCodePos === false) {
                throw new Exception("Format QRIS Statis tidak didukung (tag 5802ID tidak ditemukan)");
            }

            $beforeCountry = substr($qrisWithoutCrc, 0, $countryCodePos);
            $afterCountry = substr($qrisWithoutCrc, $countryCodePos);

            $qrisModified = $beforeCountry . $newAmountTag . $afterCountry;
        }

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
            // Panggil API GoBiz / GoPay Merchant
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->timeout(15)->get($apiUrl, [
                'merchant_id' => $merchantId,
                'limit' => 20,
                'page' => 1
            ]);

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
