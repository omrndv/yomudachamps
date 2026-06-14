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
        $active_seasons = Season::where('status', 'ACTIVE')
            ->withCount(['teams' => function ($q) {
                $q->where('status', 'PAID');
            }])->get();

        return view('landing', compact('active_seasons'));
    }

    public function registerForm()
    {
        $active_season = Season::where('status', 'ACTIVE')
            ->withCount(['teams' => function ($q) {
                $q->where('status', 'PAID');
            }])
            ->first();
    
        if (!$active_season) {
            return redirect('/')->with('error', 'Tidak ada tournament aktif saat ini.');
        }
    
        return view('register', compact('active_season'));
    }

    public function storeRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'wa_number' => 'required|min:10',
            'season_id' => 'required'
        ]);

        $existingTeam = Team::where('season_id', $request->season_id)
            ->where('wa_number', $request->wa_number)
            ->where('name', $request->name)
            ->first();

        if ($existingTeam) {
            if ($existingTeam->status == 'PAID') {
                return back()->with('error', 'Tim ini sudah terdaftar dan lunas! Silakan cek status untuk link grup.');
            } else {
                return redirect()->route('payment.confirm', $existingTeam->trx_id)
                    ->with('info', 'Pendaftaran tim ini sudah ada sebelumnya. Silakan lanjutkan pembayaran.');
            }
        }

        $season = Season::withCount(['teams' => function ($q) {
            $q->where('status', 'PAID');
        }])->findOrFail($request->season_id);

        if ($season->teams_count >= $season->slot) {
            return back()->with('error', 'Maaf, slot untuk season ini sudah penuh!');
        }

        $team = Team::create([
            'season_id' => $request->season_id,
            'trx_id'    => 'YMD' . $request->season_id . '-' . strtoupper(Str::random(5)),
            'name'      => $request->name,
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

    public function checkout(Request $request, $trx_id)
    {
        $team = Team::with('season')->where('trx_id', $trx_id)->firstOrFail();
        $method = $request->payment_method;

        $tripay = new TripayController();
        $transaction = $tripay->requestTransaction($method, $team);

        if ($transaction && $transaction->success) {
            $team->update([
                'tripay_reference' => $transaction->data->reference,
                'payment_method' => $method,
            ]);

            return redirect()->route('payment.detail', $team->trx_id);
        }

        return back()->with('error', 'Tripay Error: ' . ($transaction->message ?? 'Gagal membuat transaksi'));
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
        $url = $request->query('url');

        if (!$url) {
            return back()->with('error', 'URL QRIS tidak ditemukan.');
        }

        try {
            $name = 'QRIS_YOMUDA_' . time() . '.png';
            $content = file_get_contents($url);

            if ($content === false) {
                throw new \Exception("Gagal mengambil gambar.");
            }

            return response($content)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', "attachment; filename=$name");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh QRIS: ' . $e->getMessage());
        }
    }

    public function checkPage()
    {
        return view('check_team');
    }

    public function searchTeam(Request $request)
    {
        $request->validate([
            'wa_number' => 'required'
        ]);
        
        $wa = $request->wa_number;
    
        $teams = Team::with('season')
            ->where('wa_number', 'LIKE', '%' . $wa . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    
        if ($teams->isEmpty()) {
            return back()->with('error', 'Nomor WhatsApp tidak ditemukan. Pastikan nomornya benar ya!');
        }
    
        return back()->with('teams', $teams);
    }

    public function checkStatusAjax($trx_id)
    {
        $team = Team::where('trx_id', $trx_id)->first();
        
        if (!$team) {
            return response()->json(['error' => 'Not Found'], 404);
        }
    
        return response()->json([
            'status' => $team->status
        ]);
    }
}