<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Team;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewRegistration;
use Illuminate\Support\Facades\Notification;
use App\Models\Setting;

class IPaymuCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $va = Setting::getVal('ipaymu_va', env('IPAYMU_VA'));
        $apiKey = Setting::getVal('ipaymu_api_key', env('IPAYMU_API_KEY'));

        $signatureHeader = $request->header('signature') 
            ?? $request->header('x-signature') 
            ?? $request->server('HTTP_SIGNATURE') 
            ?? $request->server('HTTP_X_SIGNATURE') 
            ?? '';

        $json = $request->getContent();
        $data = json_decode($json, true);
        if (JSON_ERROR_NONE !== json_last_error() || !is_array($data)) {
            $data = $request->all();
        }

        // Convert null values back to empty strings (to reverse Laravel's ConvertEmptyStringsToNull middleware)
        foreach ($data as $key => $value) {
            if ($value === null) {
                $data[$key] = '';
            }
        }

        // Ambil signature yang diterima
        $receivedSignature = $signatureHeader ?: ($data['signature'] ?? '');

        // Hapus parameter signature agar tidak ikut di-hash
        unset($data['signature']);

        // Normalisasi tipe data agar identik dengan format iPaymu JSON asli
        if (isset($data['trx_id'])) {
            $data['trx_id'] = (int) $data['trx_id'];
        }
        if (isset($data['status_code'])) {
            $data['status_code'] = (int) $data['status_code'];
        }
        if (isset($data['paid_off'])) {
            $data['paid_off'] = (int) $data['paid_off'];
        }
        if (isset($data['transaction_status_code'])) {
            $data['transaction_status_code'] = (int) $data['transaction_status_code'];
        }
        if (isset($data['is_escrow'])) {
            $data['is_escrow'] = ($data['is_escrow'] === 'true' || $data['is_escrow'] === true || $data['is_escrow'] === 1 || $data['is_escrow'] === '1' || $data['is_escrow'] === 'true') ? true : false;
        } else {
            $data['is_escrow'] = false;
        }
        if (!isset($data['additional_info'])) {
            $data['additional_info'] = [];
        } else if (is_string($data['additional_info'])) {
            $data['additional_info'] = json_decode($data['additional_info'], true) ?? [];
        }

        // Urutkan key secara ascending (ksort) sesuai dokumentasi iPaymu
        ksort($data);

        // Encode ke JSON (dengan default escaping slashes)
        $jsonBody = json_encode($data);
        Log::warning('IPaymu Callback JSON body for signature: ' . $jsonBody);

        // Hitung signature dengan Secret Key berupa nomor VA iPaymu
        $calculatedSignature = hash_hmac('sha256', $jsonBody, $va);

        if (empty($receivedSignature) || !hash_equals($calculatedSignature, $receivedSignature)) {
            Log::warning('IPaymu Callback Signature Mismatch. Received: ' . $receivedSignature . ' Expected: ' . $calculatedSignature . ' VA used: ' . $va);
            return Response::json([
                'success' => false, 
                'message' => 'Invalid signature',
                'expected' => $calculatedSignature,
                'received' => $receivedSignature,
                'json_used' => $jsonBody
            ]);
        }

        // Konversi kembali ke object untuk kompatibilitas kode di bawah
        $data = (object) $data;

        $referenceId = $data->reference_id ?? null;
        if (!$referenceId) {
            return Response::json(['success' => false, 'message' => 'Missing reference_id']);
        }

        // Cari tim berdasarkan reference_id (trx_id di local db)
        $team = Team::with('season')->where('trx_id', $referenceId)->first();

        if (!$team) {
            Log::warning("IPaymu Callback: No team found for reference_id: $referenceId");
            return Response::json(['success' => true, 'message' => 'No team found']);
        }

        $statusLama = $team->status;
        $statusCode = (int) ($data->status_code ?? 0);
        $statusStr = $data->status ?? 'pending';

        // Simpan status iPaymu ke kolom status_tripay untuk menghindari perubahan skema database
        $team->status_tripay = strtoupper($statusStr);

        if ($statusCode === 1) { // Success
            $currentPaidCount = Team::where('season_id', $team->season_id)
                                ->where('status', 'PAID')
                                ->count();

            if ($currentPaidCount < $team->season->slot) {
                $team->status = 'PAID';

                if ($statusLama !== 'PAID') {
                    try {
                        $adminEmail = 'monotp94@gmail.com';
                        Notification::route('mail', $adminEmail)->notify(new NewRegistration($team));
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim email: ' . $e->getMessage());
                    }

                    // Kirim WhatsApp otomatis ke perwakilan tim
                    try {
                        \App\Services\WhatsappService::sendPaidNotification($team);
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim WhatsApp otomatis: ' . $e->getMessage());
                    }
                }
            } else {                
                $team->status = 'FAILED'; 
                Log::warning("OVER-SLOT: Tim {$team->name} bayar iPaymu tapi slot penuh.");
            }
        } elseif ($statusCode === -2) { // Expired
            $team->status = 'PENDING';
        } else {
            // Pending or other status
            $team->status = 'PENDING';
        }

        $team->save();

        return Response::json(['success' => true]);
    }
}
