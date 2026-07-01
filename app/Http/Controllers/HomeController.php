<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Team;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $active_seasons = Season::where('status', 'ACTIVE')
            ->where('name', '!=', 'Season 32')
            ->withCount(['teams' => function ($q) {
                $q->where('status', 'PAID');
            }])->get();

        $faqs = Faq::where('is_active', true)->orderBy('order', 'asc')->take(5)->get();

        return view('landing', compact('active_seasons', 'faqs'));
    }

    public function faqs()
    {
        $faqs = Faq::where('is_active', true)->orderBy('order', 'asc')->get();
        return view('faqs', compact('faqs'));
    }

    public function registerForm()
    {
        $active_season = Season::where('status', 'ACTIVE')
            ->where('name', '!=', 'Season 32')
            ->withCount(['teams' => function ($q) {
                $q->where('status', 'PAID');
            }])
            ->first();
    
        if (!$active_season) {
            return redirect('/')->with('error', 'Tidak ada tournament aktif saat ini.');
        }
    
        return view('register', compact('active_season'));
    }

    public function registerTripayForm()
    {
        $active_season = Season::where('status', 'ACTIVE')
            ->where('name', 'Season 32')
            ->withCount(['teams' => function ($q) {
                $q->where('status', 'PAID');
            }])
            ->first();
    
        if (!$active_season) {
            return redirect('/')->with('error', 'Turnamen Season 32 tidak aktif saat ini.');
        }
    
        return view('register', compact('active_season'));
    }

    public function storeRegistration(Request $request)
    {
        // Normalisasi format nomor WA
        if ($request->has('wa_number')) {
            $wa_raw = trim($request->wa_number);
            // 1. Bersihkan semua karakter non-digit kecuali tanda plus (+) di awal
            $wa_clean = preg_replace('/[^0-9+]/', '', $wa_raw);
            
            // 2. Normalisasi format nomor WA
            if (str_starts_with($wa_clean, '+62')) {
                $wa_clean = '0' . substr($wa_clean, 3);
            } elseif (str_starts_with($wa_clean, '628')) {
                $wa_clean = '0' . substr($wa_clean, 2);
            } elseif (str_starts_with($wa_clean, '+60')) {
                // Tetap menggunakan +60 di awal
            } elseif (str_starts_with($wa_clean, '60')) {
                // Tambahkan tanda + di depan jika sudah berawalan 60
                $wa_clean = '+' . $wa_clean;
            } elseif (str_starts_with($wa_clean, '01')) {
                // Jika diawali 01 (nomor Malaysia format lokal seperti 011..., 012...), ubah menjadi +601...
                $wa_clean = '+60' . substr($wa_clean, 1);
            } elseif (str_starts_with($wa_clean, '1')) {
                // Jika diawali langsung angka 1 (format lokal tanpa 0, misal 11...), ubah menjadi +601...
                $wa_clean = '+60' . $wa_clean;
            } elseif (!str_starts_with($wa_clean, '0') && !str_starts_with($wa_clean, '+') && strlen($wa_clean) > 0) {
                // Jika tidak dimulai dengan 0 atau + (misal langsung 853...), tambahkan 0 di depan (asumsi nomor Indo)
                $wa_clean = '0' . $wa_clean;
            }
            $request->merge(['wa_number' => $wa_clean]);
        }

        $request->validate([
            'name' => 'required|string|max:50',
            'wa_number' => 'required|string|min:10|max:25|regex:/^[0-9+\s\-()]+$/',
            'season_id' => 'required|integer'
        ]);

        // Validate that season_id is actually an active & open season
        $targetSeason = Season::where('id', $request->season_id)
            ->where('status', 'ACTIVE')
            ->where('is_open', true)
            ->first();

        if (!$targetSeason) {
            return back()->with('error', 'Season tidak valid atau pendaftaran sudah ditutup.');
        }

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

        if ($team->status === 'PAID') {
            return redirect()->route('payment.success', $team->trx_id);
        }

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

        if ($team->status === 'PAID') {
            return redirect()->route('payment.success', $team->trx_id);
        }

        $currentPaid = Team::where('season_id', $team->season_id)->where('status', 'PAID')->count();
        if ($currentPaid >= $team->season->slot) {
            return redirect('/')->with('error', 'Slot turnamen sudah penuh.');
        }

        $request->validate([
            'payment_method' => 'required|string'
        ]);

        $method = strtoupper(trim($request->payment_method));
        $tripay = new TripayController();
        
        $availableMethods = collect($tripay->getPaymentChannels())->pluck('code')->map(function($code){ return strtoupper($code); })->toArray();
        if (empty($method) || !in_array($method, $availableMethods)) {
            return back()->with('error', 'Metode pembayaran tidak tersedia atau tidak dipilih.');
        }

        $transaction = $tripay->requestTransaction($method, $team);

        if ($transaction && $transaction->success) {
            $team->update([
                'tripay_reference' => $transaction->data->reference,
                'payment_method'   => $method,
            ]);

            return redirect()->route('payment.detail', $team->trx_id);
        }

        return back()->with('error', 'Tripay Error: ' . ($transaction->message ?? 'Gagal membuat transaksi'));
    }

    public function paymentDetail($trx_id)
    {
        $team = Team::with('season')->where('trx_id', $trx_id)->firstOrFail();

        if ($team->status === 'PAID') {
            return redirect()->route('payment.success', $team->trx_id);
        }

        if (!$team->payment_method) {
            return redirect()->route('payment.confirm', $team->trx_id);
        }

        $tripay = new TripayController();
        $detail = $tripay->getDetailTransaction($team->tripay_reference);

        if (!$detail) {
            return redirect()->route('home');
        }

        return view('payment_detail_tripay', compact('team', 'detail'));
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

        // SSRF Protection: Only allow trusted domains
        $allowedDomains = ['tripay.co.id', 'api.qrserver.com'];
        $parsedHost = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (!$parsedHost || !in_array($scheme, ['http', 'https'])) {
            return back()->with('error', 'URL tidak valid.');
        }

        $domainAllowed = false;
        foreach ($allowedDomains as $domain) {
            if ($parsedHost === $domain || str_ends_with($parsedHost, '.' . $domain)) {
                $domainAllowed = true;
                break;
            }
        }

        if (!$domainAllowed) {
            return back()->with('error', 'Sumber QRIS tidak diizinkan.');
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
            return back()->with('error', 'Gagal mengunduh QRIS.');
        }
    }

    public function checkPage()
    {
        return view('check_team');
    }

    public function searchTeam(Request $request)
    {
        if ($request->has('wa_number')) {
            $wa_raw = trim($request->wa_number);
            // 1. Bersihkan semua karakter non-digit kecuali tanda plus (+) di awal
            $wa_clean = preg_replace('/[^0-9+]/', '', $wa_raw);
            
            // 2. Normalisasi format nomor WA
            if (str_starts_with($wa_clean, '+62')) {
                $wa_clean = '0' . substr($wa_clean, 3);
            } elseif (str_starts_with($wa_clean, '628')) {
                $wa_clean = '0' . substr($wa_clean, 2);
            } elseif (str_starts_with($wa_clean, '+60')) {
                // Tetap menggunakan +60 di awal
            } elseif (str_starts_with($wa_clean, '60')) {
                // Tambahkan tanda + di depan jika sudah berawalan 60
                $wa_clean = '+' . $wa_clean;
            } elseif (str_starts_with($wa_clean, '01')) {
                // Jika diawali 01 (nomor Malaysia format lokal seperti 011..., 012...), ubah menjadi +601...
                $wa_clean = '+60' . substr($wa_clean, 1);
            } elseif (str_starts_with($wa_clean, '1')) {
                // Jika diawali langsung angka 1 (format lokal tanpa 0, misal 11...), ubah menjadi +601...
                $wa_clean = '+60' . $wa_clean;
            } elseif (!str_starts_with($wa_clean, '0') && !str_starts_with($wa_clean, '+') && strlen($wa_clean) > 0) {
                // Jika tidak dimulai dengan 0 atau + (misal langsung 853...), tambahkan 0 di depan (asumsi nomor Indo)
                $wa_clean = '0' . $wa_clean;
            }
            $request->merge(['wa_number' => $wa_clean]);
        }

        $request->validate([
            'wa_number' => 'required|string|min:10|max:20|regex:/^[0-9+]+$/'
        ]);
        
        $wa = $request->wa_number;
    
        $teams = Team::with('season')
            ->where('wa_number', '=', $wa)
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

    public function redirectCertificate()
    {
        $active_season = \App\Models\Season::where('status', 'ACTIVE')->first();
        if (!$active_season) {
            return redirect('/')->with('error', 'Tidak ada turnamen aktif saat ini.');
        }

        $layout = \App\Models\CertificateLayout::where('season_id', $active_season->id)->first();
        if ($layout && $layout->is_released && $layout->google_drive_link) {
            return redirect()->away($layout->google_drive_link);
        }

        return redirect('/')->with('error', 'Sertifikat untuk season ini belum siap dibagikan / belum dirilis!');
    }

    public function redirectCertificateBySlug($season_slug)
    {
        $slug = strtolower(trim($season_slug));
        
        $season = \App\Models\Season::all()->first(function ($s) use ($slug) {
            $sSlug = strtolower(str_replace(' ', '-', trim($s->name)));
            if ($sSlug === $slug) {
                return true;
            }
            if (is_numeric($slug)) {
                return $sSlug === 'season-' . $slug;
            }
            return false;
        });

        if ($season) {
            $layout = \App\Models\CertificateLayout::where('season_id', $season->id)->first();
            if ($layout && $layout->is_released && $layout->google_drive_link) {
                return redirect()->away($layout->google_drive_link);
            }
        }

        return redirect('/')->with('error', 'Sertifikat untuk season tersebut belum siap dibagikan / belum dirilis!');
    }

    public function aiChat(Request $request)
    {
        $message = $request->input('message');
        if (empty($message)) {
            return response()->json(['success' => false, 'reply' => 'Pesan tidak boleh kosong.']);
        }

        $activeSeasons = \App\Models\Season::where('status', 'ACTIVE')->get();
        $adminWa = \App\Models\Setting::getVal('admin_wa', '628xxx');
        $rulesLink = \App\Models\Setting::getVal('global_rules_link', '#');

        $context = "INFORMASI WEBSITE YOMUDASCHAMPS SAAT INI:\n";
        $context .= "- Kontak Admin WhatsApp: https://wa.me/{$adminWa} ({$adminWa})\n";
        $context .= "- Link Aturan Turnamen: {$rulesLink}\n\n";

        if ($activeSeasons->isNotEmpty()) {
            $context .= "TURNAMEN AKTIF/PENDAFTARAN DIBUKA:\n";
            foreach ($activeSeasons as $s) {
                $slotsFilled = \App\Models\Team::where('season_id', $s->id)->where('status', 'PAID')->count();
                $slotsLeft = max(0, $s->slots - $slotsFilled);

                $context .= "- Nama Season: {$s->name}\n";
                $context .= "  Biaya Pendaftaran: Rp " . number_format($s->price, 0, ',', '.') . "\n";
                $context .= "  Total Slot: {$s->slots} Tim\n";
                $context .= "  Sisa Slot: {$slotsLeft} Tim\n";
                $context .= "  Syarat Pendaftaran: Tim harus melakukan pembayaran lunas via Tripay di website.\n\n";
            }
        } else {
            $context .= "Saat ini tidak ada turnamen/season aktif yang membuka pendaftaran.\n\n";
        }

        $prompt = "You are Yomuda AI, a friendly and helpful 24/7 virtual assistant for Yomuda Championship (an esports platform for Mobile Legends tournaments).\n"
                . "Your goal is to answer player questions accurately using only the provided context. If the answer is not in the context, guide them to contact the admin via the WhatsApp link provided.\n\n"
                . "Context:\n{$context}\n\n"
                . "User Question: \"{$message}\"\n\n"
                . "Instructions:\n"
                . "- Answer in Indonesian with a friendly, professional, and slightly gamer-friendly tone (use 'kamu', 'tim kamu', 'Halo Bro/Sist').\n"
                . "- Keep it concise. Emphasize WhatsApp link if they need direct help.\n"
                . "- Answer directly. Do not make up information.";

        try {
            $apiKey = \App\Models\Setting::getVal('gemini_api_key', env('GEMINI_API_KEY'));
            if (!$apiKey) {
                return response()->json([
                    'success' => true,
                    'reply' => "Halo! Saya Yomuda AI. Maaf saat ini layanan AI sedang dinonaktifkan oleh administrator. Silakan hubungi langsung admin kami via WhatsApp di https://wa.me/{$adminWa} ya!"
                ]);
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_TIMEOUT => 15
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            if (!empty($error)) {
                throw new \Exception("cURL Error: " . $error);
            }

            $resData = json_decode($response, true);
            
            if (isset($resData['error'])) {
                throw new \Exception($resData['error']['message'] ?? 'API Error');
            }

            $reply = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($reply)) {
                throw new \Exception("Empty response from AI.");
            }

            return response()->json([
                'success' => true,
                'reply' => trim($reply)
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("AI Chatbot error: " . $e->getMessage());
            return response()->json([
                'success' => true,
                'reply' => "Halo! Terjadi kendala saat menghubungi AI. Silakan tanyakan langsung ke admin kami melalui WhatsApp di https://wa.me/{$adminWa} ya! Kami siap membantu."
            ]);
        }
    }
}