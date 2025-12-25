<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $active_season = Season::where('status', 'ACTIVE')->withCount(['teams' => function ($q) {
            $q->where('status', 'PAID'); 
        }])->first();
        return view('register', compact('active_season'));
    }

    public function storeRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'wa_number' => 'required|min:10',
            'season_id' => 'required'
        ]);

        $season = Season::withCount(['teams' => function ($q) {
            $q->where('status', 'PAID');
        }])->findOrFail($request->season_id);

        if ($season->teams_count >= $season->slot) {
            return back()->with('error', 'Maaf, slot untuk season ini sudah penuh!');
        }

        $team = Team::create([
            'season_id' => $request->season_id,
            'trx_id'    => 'YMD' . $request->season_id . '-' . strtoupper(Str::random(5)),
            'name'      => strtoupper($request->name),
            'wa_number' => $request->wa_number,
            'status'    => 'PENDING'
        ]);

        return redirect()->route('payment.confirm', $team->trx_id);
    }

    public function paymentConfirm($trx_id)
    {
        $team = Team::with('season')->where('trx_id', $trx_id)->firstOrFail();

        $currentPaid = Team::where('season_id', $team->season_id)->where('status', 'PAID')->count();
        if ($currentPaid >= $team->season->slot) {
            return redirect('/')->with('error', 'Waduh telat! Slot baru saja penuh oleh pendaftar lain.');
        }

        $tripay = new TripayController();
        $channels = $tripay->getPaymentChannels();

        return view('payment', compact('team', 'channels'));
    }

    public function checkout(Request $request, $id)
    {
        $team = Team::with('season')->findOrFail($id);
        $method = $request->payment_method;

        $tripay = new TripayController();
        $transaction = $tripay->requestTransaction($method, $team);

        if ($transaction->success) {
            $team->update([
                'tripay_reference' => $transaction->data->reference,
                'payment_method' => $method,
            ]);

            return redirect()->route('payment.detail', $team->trx_id);
        }

        return back()->with('error', 'Tripay Error: ' . $transaction->message);
    }

    public function paymentDetail($trx_id)
    {
        $team = Team::where('trx_id', $trx_id)->firstOrFail();
        $tripay = new TripayController();
        $detail = $tripay->getDetailTransaction($team->tripay_reference);

        if (!$detail) return redirect()->route('index');

        return view('payment_detail', compact('team', 'detail'));
    }

    public function successPage($trx_id)
    {
        $team = Team::with('season')->where('trx_id', $trx_id)->firstOrFail();
        if ($team->status !== 'PAID') {
            return redirect()->route('payment.detail', $team->trx_id);
        }
        return view('success', compact('team'));
    }

    public function downloadQris(Request $request)
    {
        $url = $request->url;
        $name = 'QRIS_YOMUDA_' . time() . '.png';
        $content = file_get_contents($url);
        return response($content)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', "attachment; filename=$name");
    }
}