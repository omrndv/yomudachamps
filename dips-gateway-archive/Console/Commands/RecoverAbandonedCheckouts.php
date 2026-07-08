<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;
use App\Services\WhatsappService;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RecoverAbandonedCheckouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qris:recover-abandoned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim otomatis pengingat tagihan WhatsApp untuk kapten tim yang status pembayarannya PENDING lebih dari 30 menit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai pencarian pendaftaran terbengkalai (Abandoned Checkouts)...");

        // Ambil tim yang mendaftar tapi masih PENDING dalam rentang 30 menit hingga 2 jam yang lalu
        $fromTime = now()->subHours(2);
        $toTime = now()->subMinutes(30);

        $abandonedTeams = Team::with('season')
            ->where('status', 'PENDING')
            ->whereBetween('created_at', [$fromTime, $toTime])
            ->get();

        if ($abandonedTeams->isEmpty()) {
            $this->info("Tidak ada pendaftaran terbengkalai yang ditemukan.");
            return Command::SUCCESS;
        }

        $sentCount = 0;

        foreach ($abandonedTeams as $team) {
            // Hindari pengiriman ganda menggunakan Cache
            $cacheKey = 'wa_recovery_sent_' . $team->id;
            if (Cache::has($cacheKey)) {
                continue;
            }

            $checkoutUrl = route('payment.confirm', $team->trx_id);
            $adminWa = Setting::getVal('admin_wa', '0851-2261-6191');

            $message = "Halo Kapten *{$team->name}*! 🎮\n\n"
                . "Kami mendeteksi pendaftaran tim Anda untuk turnamen *Yomuda Championship {$team->season->name}* masih *PENDING* (Belum Terbayar).\n\n"
                . "Slot pendaftaran turnamen sangat terbatas dan bisa habis sewaktu-waktu. Segera amankan slot tim Anda dengan menyelesaikan pembayaran sebelum kedaluwarsa:\n"
                . "👉 *{$checkoutUrl}*\n\n"
                . "Jika ada pertanyaan atau kendala pembayaran, silakan hubungi admin di WA {$adminWa}.\n\n"
                . "Terima kasih, siapkan squad terbaikmu! 🔥\n"
                . "-- Yomuda Championship --";

            $success = WhatsappService::sendMessage($team->wa_number, $message);

            if ($success) {
                $this->info("Pengingat pembayaran terkirim ke Kapten Tim: {$team->name} ({$team->wa_number})");
                Cache::put($cacheKey, true, 86400 * 7); // Simpan status selama 7 hari
                $sentCount++;
            }
        }

        $this->info("Selesai! Berhasil mengirimkan {$sentCount} pengingat pembayaran.");
        return Command::SUCCESS;
    }
}
