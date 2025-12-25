<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Team;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewRegistration;
use Illuminate\Support\Facades\Notification;

class TripayCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $privateKey = env('TRIPAY_PRIVATE_KEY');
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        $signature = hash_hmac('sha256', $json, $privateKey);

        if ($signature !== (string) $callbackSignature) {
            return Response::json(['success' => false, 'message' => 'Invalid signature']);
        }

        if ('payment_status' !== (string) $request->server('HTTP_X_CALLBACK_EVENT')) {
            return Response::json(['success' => false, 'message' => 'Unrecognized callback event']);
        }

        $data = json_decode($json);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return Response::json(['success' => false, 'message' => 'Invalid data sent by tripay']);
        }

        $team = Team::with('season')->where('trx_id', $data->merchant_ref)
            ->where('tripay_reference', $data->reference)
            ->first();

        if (!$team) {
            return Response::json(['success' => true, 'message' => 'No team found']);
        }

        $statusLama = $team->status;
        $statusTriPay = strtoupper((string) $data->status);
        $team->status_tripay = $statusTriPay;

        switch ($statusTriPay) {
            case 'PAID':
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
                    }
                } else {                
                    $team->status = 'FAILED'; 
                    Log::warning("OVER-SLOT: Tim {$team->name} bayar tapi slot penuh. Refund manual!");
                }
                break;

            case 'EXPIRED':
            case 'FAILED':
            case 'REFUND':
                $team->status = 'PENDING';
                break;

            default:
                return Response::json(['success' => false, 'message' => 'Unrecognized status']);
        }

        $team->save();
        return Response::json(['success' => true]);
    }
}