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
        $pendingTransactions = QrisTransaction::where('status', 'PENDING')->get();
        $pendingCount = $pendingTransactions->count();
        $matchedCount = 0;

        if ($pendingTransactions->isEmpty()) {
            $this->writeSyncLog('SUKSES (Idle)', 0, 0, 0);
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
            $this->writeSyncLog('SUKSES', $pendingCount, 0, 0);
            return false;
        }

        // 3. Ambil data mutasi terakhir dari GoPay Merchant
        $mutations = [];
        $status = 'SUKSES';
        try {
            $mutations = QrisService::fetchGoPayMutations();
        } catch (Exception $e) {
            $status = 'ERROR: ' . $e->getMessage();
        }

        if (empty($mutations)) {
            $this->warn("Tidak ada data mutasi yang terbaca dari GoPay Merchant API.");
            $this->writeSyncLog($status, $pendingCount, 0, 0);
            return true;
        }

        // 4. Lakukan pencocokan nominal
        foreach ($pendingTransactions as $tx) {
            foreach ($mutations as $mutation) {
                // Konversi data mutasi (berasal dari API GoBiz)
                // API GoBiz mengembalikan nominal dengan tambahan 2 nol di belakang (cents).
                // Contoh: Rp 15.007 dikembalikan sebagai 1500700. Maka kita bagi 100.
                $rawAmount = $mutation['gross_amount'] ?? $mutation['amount'] ?? 0;
                $mutationAmount = (int) ($rawAmount / 100);
                
                $mutationStatus = strtoupper($mutation['transaction_status'] ?? $mutation['status'] ?? '');
                $gopayRef = $mutation['wallstreet_transaction_id']
                          ?? $mutation['acquiring_reference_number'] 
                          ?? $mutation['acquirer_reference_number'] 
                          ?? $mutation['reference_number'] 
                          ?? $mutation['payment_reference'] 
                          ?? $mutation['partner_payment_reference'] 
                          ?? $mutation['rrn'] 
                          ?? $mutation['transaction_id'] 
                          ?? $mutation['id'] 
                          ?? null;
                $settlementTime = isset($mutation['settlement_time']) ? Carbon::parse($mutation['settlement_time']) : now();

                // Syarat Kecocokan:
                // - Nominal akhir harus sama persis (termasuk kode unik)
                // - Status mutasi harus SETTLEMENT / SUCCESS
                // - Waktu mutasi harus setelah waktu pembuatan transaksi kita (menghindari double-settle dari transaksi lama)
                // Note: Gunakan clone() agar $tx->created_at tidak termutasi di dalam loop
                if (
                    $tx->amount === $mutationAmount &&
                    in_array($mutationStatus, ['SETTLEMENT', 'SUCCESS']) &&
                    $tx->created_at->clone()->subMinutes(10)->lte($settlementTime) // Toleransi waktu 10 menit sebelumnya
                ) {
                    $this->info("Kecocokan ditemukan! Transaksi {$tx->id} dicocokkan dengan mutasi GoPay Ref: {$gopayRef}");

                    // Selesaikan pembayaran tim
                    $this->settlePayment($tx, $gopayRef);
                    $matchedCount++;
                    break; // Keluar dari loop mutasi, lanjut ke transaksi pending berikutnya
                }
            }
        }

        $this->writeSyncLog($status, $pendingCount, $matchedCount, count($mutations));
        return true;
    }

    /**
     * Menulis status sinkronisasi ke cache
     */
    private function writeSyncLog(string $status, int $pending, int $matched, int $mutations)
    {
        $log = [
            'last_sync' => now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s'),
            'status' => $status,
            'pending_count' => $pending,
            'matched_count' => $matched,
            'mutation_count' => $mutations
        ];
        \Illuminate\Support\Facades\Cache::put('qris_last_sync_log', $log, 86400 * 7);
        \Illuminate\Support\Facades\Cache::forget('qris_anomalies_count'); // Refresh anomalies badge
    }

    /**
     * Mengubah status tim menjadi PAID & kirim notifikasi
     */
    private function settlePayment(QrisTransaction $qrisTx, string $gopayRef)
    {
        QrisService::settle($qrisTx, $gopayRef);
    }
}
