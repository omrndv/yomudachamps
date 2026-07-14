<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Team;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class IPaymuController extends Controller
{
    public function requestTransaction($team)
    {
        try {
            $va = Setting::getVal('ipaymu_va', env('IPAYMU_VA'));
            $apiKey = Setting::getVal('ipaymu_api_key', env('IPAYMU_API_KEY'));
            $mode = Setting::getVal('ipaymu_mode', env('IPAYMU_MODE', 'sandbox'));

            $url = ($mode === 'sandbox')
                ? 'https://sandbox.ipaymu.com/api/v2/payment/direct'
                : 'https://payment.ipaymu.com/api/v2/payment/direct';

            $amount = (int) $team->season->price;

            $notifyUrl = config('app.url') ? rtrim(config('app.url'), '/') . '/api/ipaymu/callback' : url('/api/ipaymu/callback');

            // Bersihkan nomor whatsapp agar hanya berisi angka
            $phone = preg_replace('/[^0-9]/', '', $team->wa_number);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            $body = [
                'name' => $team->name,
                'email' => 'player@yomuda.com',
                'phone' => $phone,
                'amount' => $amount,
                'notifyUrl' => $notifyUrl,
                'returnUrl' => route('payment.success', $team->trx_id),
                'cancelUrl' => route('payment.confirm', $team->trx_id),
                'paymentMethod' => 'qris',
                'paymentChannel' => 'qris',
                'referenceId' => $team->trx_id,
            ];

            $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $timestamp = date('YmdHis');
            $requestBodyHash = strtolower(hash('sha256', $jsonBody));
            $stringToSign = "POST:$va:$requestBodyHash:$apiKey";
            $signature = hash_hmac('sha256', $stringToSign, $apiKey);

            $headers = [
                'Content-Type: application/json',
                'va: ' . $va,
                'signature: ' . $signature,
                'timestamp: ' . $timestamp
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_FRESH_CONNECT  => true,
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $jsonBody,
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            if (!empty($error)) {
                throw new Exception("cURL Error: " . $error);
            }

            $result = json_decode($response);

            // Handle keys (bisa Status/status, Data/data, Message/message)
            $resStatus = $result->Status ?? $result->status ?? null;
            $resData = $result->Data ?? $result->data ?? null;
            $resMessage = $result->Message ?? $result->message ?? null;

            if ($resStatus == 200 && $resData && (isset($resData->QrImage) || isset($resData->qr_image))) {
                return (object)[
                    'success' => true,
                    'transaction_id' => $resData->TransactionId ?? $resData->transaction_id ?? '',
                    'qr_image' => $resData->QrImage ?? $resData->qr_image ?? '',
                    'qr_string' => $resData->QrString ?? $resData->qr_string ?? '',
                    'expired' => $resData->Expired ?? $resData->expired ?? '',
                    'message' => 'Success'
                ];
            }

            $msg = $resMessage ?? 'Gagal membuat transaksi ke iPaymu';
            if ($response) {
                $msg .= ' (API Response: ' . $response . ')';
            }
            Log::error('IPaymu Request Transaction Failed: ' . $response);
            return (object)[
                'success' => false,
                'message' => $msg
            ];

        } catch (Exception $e) {
            Log::error('IPaymu Exception: ' . $e->getMessage());
            return (object)[
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function checkTransactionStatus($transactionId)
    {
        try {
            $va = Setting::getVal('ipaymu_va', env('IPAYMU_VA'));
            $apiKey = Setting::getVal('ipaymu_api_key', env('IPAYMU_API_KEY'));
            $mode = Setting::getVal('ipaymu_mode', env('IPAYMU_MODE', 'sandbox'));

            $url = ($mode === 'sandbox')
                ? 'https://sandbox.ipaymu.com/api/v2/transaction'
                : 'https://payment.ipaymu.com/api/v2/transaction';

            $body = [
                'transactionId' => $transactionId
            ];

            $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $timestamp = date('YmdHis');
            $requestBodyHash = strtolower(hash('sha256', $jsonBody));
            $stringToSign = "POST:$va:$requestBodyHash:$apiKey";
            $signature = hash_hmac('sha256', $stringToSign, $apiKey);

            $headers = [
                'Content-Type: application/json',
                'va: ' . $va,
                'signature: ' . $signature,
                'timestamp: ' . $timestamp
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_FRESH_CONNECT  => true,
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $jsonBody,
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            return json_decode($response);
        } catch (Exception $e) {
            Log::error('IPaymu Check Transaction Exception: ' . $e->getMessage());
            return null;
        }
    }
}
