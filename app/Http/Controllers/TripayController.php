<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Team;
use Illuminate\Support\Facades\Log;

class TripayController extends Controller
{
    public function getPaymentChannels()
    {
        try {
            $apiKey = env('TRIPAY_API_KEY');
            $url = env('TRIPAY_MODE') === 'sandbox'
                ? 'https://tripay.co.id/api-sandbox/merchant/payment-channel'
                : 'https://tripay.co.id/api/merchant/payment-channel';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_FRESH_CONNECT  => true,
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
                CURLOPT_FAILONERROR    => false,
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
            ));

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            if (!empty($error)) throw new Exception("cURL Error: " . $error);

            $data = json_decode($response);
            return $data->success ? $data->data : [];
        } catch (Exception $e) {
            Log::error('Tripay Channels Error: ' . $e->getMessage());
            return [];
        }
    }

    public function requestTransaction($method, $team)
    {
        $apiKey       = env('TRIPAY_API_KEY');
        $privateKey   = env('TRIPAY_PRIVATE_KEY');
        $merchantCode = env('TRIPAY_MERCHANT_CODE');

        $merchantRef  = $team->trx_id;
        $amount       = (int) $team->season->price;

        $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

        $data = [
            'method'         => $method,
            'merchant_ref'   => $merchantRef,
            'amount'         => $amount,
            'customer_name'  => $team->name,
            'customer_email' => 'player@yomuda.com',
            'customer_phone' => $team->wa_number,
            'order_items'    => [['name' => 'Registrasi ' . $team->season->name, 'price' => $amount, 'quantity' => 1]],
            'expired_time'   => (time() + (60 * 60)), // 1 JAM
            'signature'      => $signature,
        ];

        $url = env('TRIPAY_MODE') === 'sandbox'
            ? 'https://tripay.co.id/api-sandbox/transaction/create'
            : 'https://tripay.co.id/api/transaction/create';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function getDetailTransaction($reference)
    {
        $apiKey = env('TRIPAY_API_KEY');
        $url = env('TRIPAY_MODE') === 'sandbox'
            ? 'https://tripay.co.id/api-sandbox/transaction/detail?'
            : 'https://tripay.co.id/api/transaction/detail?';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $url . http_build_query(['reference' => $reference]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $apiKey],
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);
        return $res->success ? $res->data : null;
    }
}
