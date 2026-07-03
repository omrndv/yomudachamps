<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;
use App\Models\QrisTransaction;
use App\Services\QrisService;
use App\Notifications\NewRegistration;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Exception;

class PollGoPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qris:poll {--daemon : Jalankan terus menerus dalam 1 menit dengan jeda 5 detik}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan polling mutasi GoPay Merchant untuk mencocokkan transaksi QRIS PENDING';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDaemon = $this->option('daemon');

        if ($isDaemon) {
            $this->info("Menjalankan Poller QRIS GoPay dalam mode Daemon (55 detik)...");
            $startTime = time();

            // Loop berjalan selama maksimal 55 detik agar tidak bentrok dengan cron job berikutnya
            while (time() - $startTime < 55) {
                $hasPending = $this->poll();
                
                // Jika tidak ada transaksi pending, matikan daemon lebih cepat untuk menghemat CPU (Idle Auto-Stop)
                if (!$hasPending) {
                    $this->info("Tidak ada transaksi PENDING. Poller berhenti otomatis (idle).");
                    break;
                }

                sleep(5);
            }
        } else {
            $this->info("Menjalankan Poller QRIS GoPay (Single Run)...");
            $this->poll();
        }

        return Command::SUCCESS;
    }

    /**
     * Logika utama pencocokan mutasi
     */
    private function poll(): bool
    {
        // 1. Ambil semua transaksi QRIS berstatus PENDING
        $pendingTransactions = QrisTransaction::where('status', 'PENDING')->get();

        if ($pendingTransactions->isEmpty()) {
            return false;
        }

        // 2. Tandai transaksi yang melewati batas waktu expired
        foreach ($pendingTransactions as $tx) {
            if ($tx->expires_at->isPast()) {
                $tx->update(['status' => 'EXPIRED']);
                $this->info("Transaksi ID {$tx->id} ditandai EXPIRED.");
            }
        }

        // Refresh list pending setelah membuang yang expired
        $pendingTransactions = QrisTransaction::where('status', 'PENDING')->get();
        if ($pendingTransactions->isEmpty()) {
            return false;
        }

        // 3. Ambil data mutasi terakhir dari GoPay Merchant
        $mutations = QrisService::fetchGoPayMutations();

        if (empty($mutations)) {
            $this->warn("Tidak ada data mutasi yang terbaca dari GoPay Merchant API.");
            return true;
        }

        // 4. Lakukan pencocokan nominal
        foreach ($pendingTransactions as $tx) {
            foreach ($mutations as $mutation) {
                // Konversi data mutasi (biasanya berasal dari API GoBiz)
                // GoBiz API mengembalikan nominal dalam bentuk "cents" (dikalikan 100).
                // Contoh: Rp 15.024 dikembalikan sebagai 1502400. Maka kita bagi 100.
                $rawAmount = $mutation['gross_amount'] ?? $mutation['amount'] ?? 0;
                $mutationAmount = (int) ($rawAmount / 100);
                
                $mutationStatus = strtoupper($mutation['transaction_status'] ?? $mutation['status'] ?? '');
                $gopayRef = $mutation['id'] ?? $mutation['transaction_id'] ?? null;
                $settlementTime = isset($mutation['settlement_time']) ? Carbon::parse($mutation['settlement_time']) : now();

                // Syarat Kecocokan:
                // - Nominal akhir harus sama persis (termasuk kode unik)
                // - Status mutasi harus SETTLEMENT / SUCCESS
                // - Waktu mutasi harus setelah waktu pembuatan transaksi kita (menghindari double-settle dari transaksi lama)
                if (
                    $tx->amount === $mutationAmount &&
                    in_array($mutationStatus, ['SETTLEMENT', 'SUCCESS']) &&
                    $tx->created_at->subMinutes(5)->lte($settlementTime) // Toleransi waktu 5 menit sebelumnya
                ) {
                    $this->info("Kecocokan ditemukan! Transaksi {$tx->id} dicocokkan dengan mutasi GoPay Ref: {$gopayRef}");

                    // Selesaikan pembayaran tim
                    $this->settlePayment($tx, $gopayRef);
                    break; // Keluar dari loop mutasi, lanjut ke transaksi pending berikutnya
                }
            }
        }

        return true;
    }

    /**
     * Mengubah status tim menjadi PAID & kirim notifikasi
     */
    private function settlePayment(QrisTransaction $qrisTx, string $gopayRef)
    {
        $team = Team::with('season')->where('trx_id', $qrisTx->trx_id)->first();

        if (!$team) {
            $qrisTx->update([
                'status' => 'PAID',
                'paid_at' => now(),
                'gopay_reference' => $gopayRef
            ]);
            return;
        }

        $statusLama = $team->status;
        $team->status_tripay = 'PAID';

        $currentPaidCount = Team::where('season_id', $team->season_id)
            ->where('status', 'PAID')
            ->count();

        if ($currentPaidCount < $team->season->slot) {
            $team->status = 'PAID';

            if ($statusLama !== 'PAID') {
                // Kirim notifikasi email ke admin
                try {
                    $adminEmail = 'monotp94@gmail.com';
                    Notification::route('mail', $adminEmail)->notify(new NewRegistration($team));
                } catch (Exception $e) {
                    Log::error('Gagal kirim email (auto poll): ' . $e->getMessage());
                }

                // Kirim WhatsApp otomatis ke perwakilan tim
                try {
                    \App\Services\WhatsappService::sendPaidNotification($team);
                } catch (Exception $e) {
                    Log::error('Gagal kirim WhatsApp otomatis (auto poll): ' . $e->getMessage());
                }
            }
        } else {
            $team->status = 'FAILED';
            Log::warning("OVER-SLOT AUTO: Tim {$team->name} terdeteksi bayar tapi slot penuh.");

            // Kirim Notifikasi Over-slot
            try {
                \App\Services\WhatsappService::sendAdminOverSlotNotification($team);
                \App\Services\WhatsappService::sendUserOverSlotNotification($team);
            } catch (Exception $e) {
                Log::error('Gagal kirim notifikasi Over-Slot (auto poll): ' . $e->getMessage());
            }
        }

        $team->save();

        // Update status transaksi QRIS
        $qrisTx->update([
            'status' => 'PAID',
            'paid_at' => now(),
            'gopay_reference' => $gopayRef
        ]);
    }
}
