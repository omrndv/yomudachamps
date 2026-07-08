<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class QrisAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Gunakan otentikasi standard Laravel Admin
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Cek perizinan akses "settings" (Super Admin yang bisa kelola sistem)
        if (!Auth::user()->hasPermission('settings')) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka Dips Gateway.');
        }

        // Hitung anomali dengan cache 60 detik agar page load cepat
        $anomalyCount = \Illuminate\Support\Facades\Cache::remember('qris_anomalies_count', 60, function() {
            $count = 0;
            try {
                // Jangan pakai cache internal di fetchGoPayMutations agar fresh per menit
                \Illuminate\Support\Facades\Cache::forget('gopay_mutations_api_cache');
                $mutations = \App\Services\QrisService::fetchGoPayMutations(50);
                if (!empty($mutations)) {
                    $dbReferences = \App\Models\QrisTransaction::whereNotNull('gopay_reference')
                        ->pluck('gopay_reference')
                        ->toArray();

                    foreach ($mutations as $m) {
                        $refId = $m['wallstreet_transaction_id']
                               ?? $m['acquiring_reference_number'] 
                               ?? $m['acquirer_reference_number'] 
                               ?? $m['reference_number'] 
                               ?? $m['payment_reference'] 
                               ?? $m['partner_payment_reference'] 
                               ?? $m['rrn'] 
                               ?? $m['transaction_id'] 
                               ?? $m['id'] 
                               ?? null;
                        $status = strtoupper($m['transaction_status'] ?? $m['status'] ?? '');

                        if ($refId && !in_array($refId, $dbReferences) && in_array($status, ['SETTLEMENT', 'CAPTURE', 'PAID', 'SUCCESS'])) {
                            $rawAmount = $m['gross_amount'] ?? $m['amount'] ?? 0;
                            $amount = (int) ($rawAmount / 100);

                            $suspectsExists = \App\Models\QrisTransaction::where('amount', $amount)
                                ->where('status', '!=', 'PAID')
                                ->exists();

                            if ($suspectsExists) {
                                $count++;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {}
            return $count;
        });

        view()->share('qrisAnomalyCount', $anomalyCount);

        return $next($request);
    }
}
