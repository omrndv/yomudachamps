<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class WhatsappService
{
    /**
     * Kirim pesan teks umum menggunakan Fonnte API
     *
     * @param string $target
     * @param string $message
     * @return bool
     */
    public static function sendMessage($target, $message)
    {
        $token = Setting::getVal('fonnte_token', env('FONNTE_TOKEN'));
        $enabledVal = Setting::getVal('wa_notification_enabled', env('WA_NOTIFICATION_ENABLED', false));
        $enabled = ($enabledVal === true || $enabledVal === 'true' || $enabledVal === 1 || $enabledVal === '1');

        if (!$enabled || !$token) {
            Log::info("WhatsApp Notification is disabled or token is missing.");
            return false;
        }

        // Bersihkan spasi atau karakter non-angka dari nomor target
        $target = preg_replace('/[^0-9]/', '', $target);

        // Ubah format nomor 08xx menjadi 628xx
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
            ]);

            $result = $response->json();
            
            if ($response->successful() && isset($result['status']) && $result['status'] == true) {
                Log::info("WhatsApp message sent successfully to {$target}.");
                return true;
            }

            Log::error("Fonnte API Error: " . json_encode($result));
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp Service Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi pembayaran lunas (PAID) ke perwakilan tim
     *
     * @param \App\Models\Team $team
     * @return bool
     */
    public static function sendPaidNotification($team)
    {
        $waLink = $team->season->wa_link;
        
        $message = "Halo *" . $team->name . "*! 🎮\n\n";
        $message .= "Pembayaran pendaftaran turnamen *Yomuda Championship " . $team->season->name . "* telah *BERHASIL DIVERIFIKASI* (Lunas) dengan ID Transaksi: " . $team->trx_id . ".\n\n";
        
        if ($waLink) {
            $message .= "Silakan bergabung ke Grup Koordinasi WhatsApp peserta untuk info bagan dan jadwal tanding:\n👉 " . $waLink . "\n\n";
        } else {
            $message .= "Grup koordinasi turnamen sedang dipersiapkan. Admin kami akan segera menghubungimu.\n\n";
        }
        
        $adminWa = Setting::getVal('admin_wa', '0851-2261-6191');
        $message .= "Terima kasih telah bergabung, siapkan squad terbaikmu! 🔥\n\n";
        $message .= "Kalau mau tanya-tanya bisa hubungi admin ke " . $adminWa . " yaa.\n\n";
        $message .= "-- Yomuda Championship --";
        
        return self::sendMessage($team->wa_number, $message);
    }
}
