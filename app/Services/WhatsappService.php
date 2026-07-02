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
        $adminWa = Setting::getVal('admin_wa', '0851-2261-6191');

        $defaultTemplate = "Halo *{nama_tim}*! 🎮\n\n"
            . "Pembayaran pendaftaran turnamen *Yomuda Championship {nama_season}* telah *BERHASIL DIVERIFIKASI* (Lunas) dengan ID Transaksi: {id_transaksi}.\n\n"
            . "{grup_info}"
            . "Terima kasih telah bergabung, siapkan squad terbaikmu! 🔥\n\n"
            . "Kalau mau tanya-tanya bisa hubungi admin ke {nomor_admin} yaa.\n\n"
            . "-- Yomuda Championship --";

        $template = Setting::getVal('wa_template_paid', $defaultTemplate);

        // Build default {grup_info} text
        if ($waLink) {
            $grupInfo = "Silakan bergabung ke Grup Koordinasi WhatsApp peserta untuk info bagan dan jadwal tanding:\n👉 " . $waLink . "\n\n";
        } else {
            $grupInfo = "Grup koordinasi turnamen sedang dipersiapkan. Admin kami akan segera menghubungimu.\n\n";
        }

        // Replacements dictionary
        $replacements = [
            '{nama_tim}' => $team->name,
            '{nama_season}' => $team->season->name,
            '{id_transaksi}' => $team->trx_id,
            '{link_grup}' => $waLink ?? '-',
            '{nomor_admin}' => $adminWa,
            '{grup_info}' => $grupInfo,
            '{harga}' => number_format($team->season->price, 0, ',', '.')
        ];

        $message = str_replace(array_keys($replacements), array_values($replacements), $template);
        
        return self::sendMessage($team->wa_number, $message);
    }

    /**
     * Kirim notifikasi ke admin ketika terjadi over-slot pembayaran
     */
    public static function sendAdminOverSlotNotification($team)
    {
        $adminWa = Setting::getVal('admin_wa', '6285122616191');
        $message = "⚠️ *PEMBERITAHUAN OVER-SLOT* ⚠️\n\n"
            . "Terjadi pembayaran masuk setelah slot turnamen penuh!\n\n"
            . "• *Nama Tim*: {nama_tim}\n"
            . "• *Season*: {nama_season}\n"
            . "• *Metode Bayar*: {metode}\n"
            . "• *ID Transaksi (TRX)*: {id_transaksi}\n"
            . "• *WhatsApp Kapten*: {nomor_kapten}\n\n"
            . "Status tim otomatis disetel menjadi *FAILED*. Mohon segera hubungi kapten tim di atas untuk melakukan refund manual.\n\n"
            . "-- Sistem Yomuda Champs --";

        $replacements = [
            '{nama_tim}' => $team->name,
            '{nama_season}' => $team->season->name,
            '{metode}' => $team->payment_method ?? '-',
            '{id_transaksi}' => $team->trx_id,
            '{nomor_kapten}' => $team->wa_number
        ];

        $message = str_replace(array_keys($replacements), array_values($replacements), $message);
        return self::sendMessage($adminWa, $message);
    }

    /**
     * Kirim notifikasi ke user bahwa pembayaran mereka over-slot dan akan di-refund
     */
    public static function sendUserOverSlotNotification($team)
    {
        $adminWa = Setting::getVal('admin_wa', '6285122616191');
        $message = "Halo Kapten tim *{nama_tim}*,\n\n"
            . "Kami mendeteksi pembayaran pendaftaran turnamen *Yomuda Championship {nama_season}* sebesar *Rp {harga}* telah sukses.\n\n"
            . "Namun mohon maaf sebesar-besarnya, *SLOT TURNAMEN TELAH PENUH* oleh pendaftar lain sesaat sebelum pembayaran Anda terverifikasi oleh sistem kami.\n\n"
            . "Sistem kami telah membatalkan otomatis pendaftaran tim Anda. Silakan hubungi admin kami melalui WhatsApp untuk proses *pengembalian dana (refund) 100%* secara manual:\n"
            . "👉 wa.me/{nomor_admin}\n\n"
            . "Mohon sertakan bukti transfer dan ID Transaksi Anda: *{id_transaksi}*.\n\n"
            . "Terima kasih atas pengertiannya,\n"
            . "-- Yomuda Championship --";

        $replacements = [
            '{nama_tim}' => $team->name,
            '{nama_season}' => $team->season->name,
            '{harga}' => number_format($team->season->price, 0, ',', '.'),
            '{id_transaksi}' => $team->trx_id,
            '{nomor_admin}' => preg_replace('/[^0-9]/', '', $adminWa)
        ];

        $message = str_replace(array_keys($replacements), array_values($replacements), $message);
        return self::sendMessage($team->wa_number, $message);
    }
}
