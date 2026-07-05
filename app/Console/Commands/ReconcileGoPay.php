<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QrisTransaction;
use App\Services\QrisService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class ReconcileGoPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qris:reconcile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan audit rekonsiliasi otomatis harian/berkala mencocokkan mutasi GoPay dengan database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai proses audit rekonsiliasi Dips Gateway GoPay...");
        
        try {
            // Ambil 100 mutasi terbaru dari GoPay Merchant
            $mutations = QrisService::fetchGoPayMutations(100);
        } catch (Exception $e) {
            $this->error("Gagal mengambil mutasi GoPay Merchant: " . $e->getMessage());
            Log::error("Reconciliation failed fetching mutations: " . $e->getMessage());
            return Command::FAILURE;
        }

        if (empty($mutations)) {
            $this->info("Tidak ada mutasi terdeteksi dari GoPay Merchant.");
            return Command::SUCCESS;
        }

        $dbReferences = QrisTransaction::whereNotNull('gopay_reference')
            ->pluck('gopay_reference')
            ->toArray();

        $correctedCount = 0;
        $matchedCount = 0;
        $anomaliesCount = 0;

        foreach ($mutations as $m) {
            $refId = $m['wallstreet_transaction_id'] ?? $m['id'] ?? null;
            $rawAmount = $m['gross_amount'] ?? $m['amount'] ?? 0;
            $amount = (int)($rawAmount / 100);
            $status = strtoupper($m['transaction_status'] ?? $m['status'] ?? '');
            
            if (!$refId || !in_array($status, ['SETTLEMENT', 'SUCCESS'])) {
                continue;
            }

            // Cari apakah transaksi ini sudah tercatat sebagai lunas dengan reference tersebut
            $isMatched = in_array($refId, $dbReferences);

            if ($isMatched) {
                $matchedCount++;
                continue;
            }

            // Jika belum tercocokkan dengan reference, coba cari transaksi pending/expired dengan nominal yang persis sama
            $dbTx = QrisTransaction::with('team')
                ->where('amount', $amount)
                ->whereIn('status', ['PENDING', 'EXPIRED'])
                ->latest()
                ->first();

            if ($dbTx) {
                // Koreksi status menjadi lunas secara otomatis (Auto-Reconcile Settle)
                $this->info("Mengkoreksi transaksi ID {$dbTx->id} (Tim: {$dbTx->team->name}) dengan nominal Rp " . number_format($amount) . " menjadi LUNAS.");
                QrisService::settle($dbTx, $refId);
                $correctedCount++;
            } else {
                // Jika ada mutasi GoPay tapi tidak ada transaksi kita yang cocok nominalnya
                $this->warn("ANOMALI: Uang masuk Rp " . number_format($amount) . " dengan Ref {$refId} terdeteksi di GoPay tapi tidak cocok dengan data pendaftaran mana pun.");
                $anomaliesCount++;
            }
        }

        $this->info("Audit Rekonsiliasi Selesai!");
        $this->info("- Mutasi Tercocokkan: {$matchedCount}");
        $this->info("- Koreksi Lunas Otomatis: {$correctedCount}");
        $this->info("- Anomali Tidak Dikenal: {$anomaliesCount}");

        // Simpan log rekonsiliasi ke activity log
        try {
            \App\Models\AdminActivity::log(
                "Audit Rekonsiliasi GoPay: Tercocokkan {$matchedCount}, Koreksi Lunas {$correctedCount}, Anomali {$anomaliesCount}."
            );
        } catch (\Exception $e) {
            // Ignore if activity log is not available in standalone execution
        }

        return Command::SUCCESS;
    }
}
