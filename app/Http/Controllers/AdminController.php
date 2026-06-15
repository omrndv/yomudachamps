<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function login()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard.home');
        }
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $admin_user = 'admin';
        $admin_pass = 'yomuda123';

        if ($request->username === $admin_user && $request->password === $admin_pass) {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard.home');
        }

        return back()->with('error', 'Username atau Password salah!');
    }

    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }

    public function dashboardHome()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        // Statistik Global
        $total_registered_teams = Team::count();
        $total_paid_teams = Team::where('status', 'PAID')->count();
        $total_active_seasons = Season::where('status', 'ACTIVE')->count();
        
        // Pendapatan Global
        $total_income = Team::where('status', 'PAID')->with('season')->get()->sum(function($t) {
            return $t->season->price;
        });

        // 5 Transaksi Lunas Terbaru
        $recent_payments = Team::with('season')
            ->where('status', 'PAID')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Semua Season beserta sisa slot terisi
        $seasons = Season::withCount(['teams' => function($q) {
            $q->where('status', 'PAID');
        }])->orderBy('created_at', 'desc')->get();

        // Trend Registrasi 7 Hari Terakhir
        $chart_labels = [];
        $chart_registered = [];
        $chart_paid = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chart_labels[] = $date->format('d M');
            
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
            
            $chart_registered[] = Team::whereBetween('created_at', [$start, $end])->count();
            $chart_paid[] = Team::where('status', 'PAID')->whereBetween('created_at', [$start, $end])->count();
        }

        return view('admin.dashboard_home', compact(
            'total_registered_teams',
            'total_paid_teams',
            'total_active_seasons',
            'total_income',
            'recent_payments',
            'seasons',
            'chart_labels',
            'chart_registered',
            'chart_paid'
        ));
    }

    public function seasons()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $seasons = Season::withCount('teams')
            ->orderBy('status', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.seasons', compact('seasons'));
    }

    public function dashboard($season_id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }
    
        $current_season = Season::findOrFail($season_id);
        $teams = Team::where('season_id', $season_id)->get();
        
        $filtered_teams = $teams->map(function ($team) use ($season_id) {
            $history = \App\Models\Team::where('wa_number', $team->wa_number)
                ->where('season_id', '!=', $season_id) // Kecuali season sekarang
                ->with('season') // Asumsi ada relasi 'season' di Model Team
                ->get();
    
            $team->history = $history;
            $team->is_loyal = $history->count() > 0;
            return $team;
        });
    
        $paid_teams = $filtered_teams->where('status', 'PAID');
        $total_income = $paid_teams->count() * $current_season->price;
    
        return view('admin.dashboard', compact(
            'current_season',
            'filtered_teams',
            'paid_teams',
            'total_income'
        ));
    }

    public function settings()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $keys = [
            'admin_wa',
            'admin_email',
            'fonnte_token',
            'wa_notification_enabled',
            'tripay_api_key',
            'tripay_private_key',
            'tripay_merchant_code',
            'tripay_mode',
            'ipaymu_va',
            'ipaymu_api_key',
            'ipaymu_mode',
            'social_instagram',
            'social_tiktok',
            'social_youtube',
            'maintenance_secret'
        ];

        foreach ($keys as $key) {
            $val = $request->input($key);
            if ($key === 'wa_notification_enabled') {
                $val = $request->has('wa_notification_enabled') ? 'true' : 'false';
            }
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val]
            );
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $publicPath = is_dir(base_path('../public_html')) 
                ? base_path('../public_html') 
                : public_path();
            $logoPath = $publicPath . '/images';
            if (!file_exists($logoPath)) {
                mkdir($logoPath, 0755, true);
            }
            $file = $request->file('logo');
            $file->move($logoPath, 'logo-yomuda.png');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $publicPath = is_dir(base_path('../public_html')) 
                ? base_path('../public_html') 
                : public_path();
            $file = $request->file('favicon');
            $file->move($publicPath, 'favicon.ico');
        }

        $secret = $request->input('maintenance_secret', 'yomudasecret');

        if ($request->has('maintenance_mode')) {
            if (!app()->isDownForMaintenance()) {
                \Illuminate\Support\Facades\Artisan::call('down', [
                    '--secret' => $secret
                ]);
            }
            $response = redirect()->back()->with('success', 'Pengaturan berhasil diperbarui dan Mode Maintenance telah AKTIF.');
            $response->withCookie(cookie('laravel_maintenance', $secret, 2628000));
        } else {
            if (app()->isDownForMaintenance()) {
                \Illuminate\Support\Facades\Artisan::call('up');
            }
            $response = redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
            $response->withCookie(cookie()->forget('laravel_maintenance'));
        }

        return $response;
    }

    public function bulkStore(Request $request, $season_id)
    {
        $rawData = $request->input('bulk_data');
        $lines = explode("\n", str_replace("\r", "", $rawData));
    
        foreach ($lines as $line) {
            if (!trim($line)) continue;
    
            $data = preg_split('/\t+/', $line);
            if (count($data) < 2) {
                $data = preg_split('/\s+(?=\d+$)/', trim($line));
            }
    
            if (count($data) >= 2) {
                $wa_raw = trim($data[1]);
                if (!str_starts_with($wa_raw, '0') && !str_starts_with($wa_raw, '62') && !str_starts_with($wa_raw, '+')) {
                    $wa_raw = '0' . $wa_raw;
                }
    
                Team::create([
                    'season_id' => $season_id,
                    'trx_id'    => 'YMD' . $season_id . '-' . strtoupper(Str::random(4)),
                    'name'      => trim($data[0]),
                    'wa_number' => $wa_raw, 
                    'status'    => 'PAID'
                ]);
            }
        }
    
        return back()->with('success', 'Data berhasil diimport ke Database!');
    }

    public function deleteTeam($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Team::findOrFail($id)->delete();
        return back()->with('success', 'Tim berhasil dihapus!');
    }

    public function deleteAllTeams($season_id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Team::where('season_id', $season_id)->delete();
        return back()->with('success', 'Semua tim berhasil dihapus!');
    }

    public function storeSeason(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $data = [
            'name'       => $request->name,
            'status'     => 'ACTIVE',
            'date_info'  => $request->date_info,
            'wa_link'    => $request->wa_link,
            'price'      => $request->price,
            'slot'       => $request->slot,
            'prize_pool' => $request->prize_pool,
            'is_open'    => $request->is_open ?? 1,
        ];

        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destination = is_dir(base_path('../public_html')) 
                ? base_path('../public_html/storage/posters') 
                : public_path('storage/posters');
            
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $data['poster'] = $filename;
        }

        Season::create($data);
        return back()->with('success', 'Season baru berhasil dibuat!');
    }

    public function updateSeason(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $season = Season::findOrFail($id);
        $data = [
            'name'       => $request->name,
            'status'     => $request->status,
            'date_info'  => $request->date_info,
            'wa_link'    => $request->wa_link,
            'price'      => $request->price,
            'slot'       => $request->slot,
            'prize_pool' => $request->prize_pool,
            'is_open'    => $request->is_open,
        ];

        if ($request->hasFile('poster')) {
            $destination = is_dir(base_path('../public_html')) 
                ? base_path('../public_html/storage/posters') 
                : public_path('storage/posters');

            if ($season->poster) {
                $oldPath = $destination . '/' . $season->poster;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('poster');
            $filename = time() . '_' . $file->getClientOriginalName();

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $data['poster'] = $filename;
        }

        $season->update($data);
        return back()->with('success', 'Season berhasil diperbarui!');
    }

    public function deleteSeason($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $season = Season::findOrFail($id);
        if ($season->poster) {
            $destination = is_dir(base_path('../public_html')) 
                ? base_path('../public_html/storage/posters') 
                : public_path('storage/posters');
            $path = $destination . '/' . $season->poster;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $season->delete();
        return back()->with('success', 'Season berhasil dihapus!');
    }

    public function updateTeam(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $team = Team::findOrFail($id);
        $statusLama = $team->status;
        
        $team->update([
            'name'      => $request->name,
            'wa_number' => $request->wa_number,
            'status'    => $request->status,
        ]);

        if ($statusLama !== 'PAID' && $request->status === 'PAID') {
            try {
                \App\Services\WhatsappService::sendPaidNotification($team);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal kirim WhatsApp saat updateTeam manual: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Data tim berhasil diperbarui!');
    }
    
    public function showNotes(Request $request) 
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $all_notes = \App\Models\AdminNote::orderBy('updated_at', 'desc')->get();

        $current_note = null;
        if ($request->has('id')) {
            $current_note = \App\Models\AdminNote::find($request->id);
        } else {
            $current_note = \App\Models\AdminNote::orderBy('updated_at', 'desc')->first();
        }

        return view('admin.notes', compact('all_notes', 'current_note'));
    }

    public function storeNote(Request $request) 
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $note = \App\Models\AdminNote::create([
            'title'   => $request->query('title', 'Catatan Baru'),
            'content' => ''
        ]);

        return redirect()->route('admin.notes.index', ['id' => $note->id])
                         ->with('success', 'Catatan baru berhasil dibuat!');
    }

    public function updateNotes(Request $request, $id)
    {
        if (!session('admin_logged_in')) return response()->json(['error' => 'Unauthorized'], 401);

        $note = \App\Models\AdminNote::findOrFail($id);
        $note->update([
            'title'   => $request->title,
            'content' => $request->content
        ]);

        return response()->json([
            'status'     => 'success',
            'updated_at' => $note->updated_at->format('H:i:s, d M Y')
        ]);
    }
    
    public function deleteNote($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        \App\Models\AdminNote::findOrFail($id)->delete();
        return redirect()->route('admin.notes.index')->with('success', 'Catatan berhasil dihapus!');
    }
    
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->team_ids);
        if ($ids) {
            Team::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' tim berhasil dihapus.');
        }
        return back()->with('error', 'Tidak ada tim yang dipilih.');
    }
    
        // FUNGSI UTAMA VIEW (Kembali ke DB Lokal agar Filter & Search Jalan)
    public function paymentHistory(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
    
        $start_date = $request->get('start_date', date('Y-m-01'));
        $end_date = $request->get('end_date', date('Y-m-t'));
        $season_id = $request->get('season_id');
    
        $query = Team::with('season')->where('status', 'PAID')
                     ->whereBetween('updated_at', [$start_date . " 00:00:00", $end_date . " 23:59:59"]);
    
        if ($season_id) $query->where('season_id', $season_id);
    
        if ($request->get('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")->orWhere('trx_id', 'LIKE', "%$search%");
            });
        }
    
        // Clone query untuk total perhitungan agar tidak terganggu pagination limit
        $total_query = clone $query;
        $total_trx = $total_query->count();
        $total_cuan = $total_query->get()->sum(function($pay) {
            return $pay->amount ?? $pay->season->price ?? 0;
        });

        // Urutan: Terbaru ke Terlama (desc)
        $payments = $query->orderBy('updated_at', 'desc')->paginate(20)->appends($request->all());
        $seasons = \App\Models\Season::orderBy('created_at', 'desc')->get();
    
        return view('admin.payment_history', compact('payments', 'total_cuan', 'total_trx', 'start_date', 'end_date', 'seasons', 'season_id'));
    }
    
    // FUNGSI SINKRONISASI (MAGIC BUTTON)
    public function syncPayments()
    {
        try {
            $pendingTeams = Team::where('status', 'PENDING')
                ->whereNotNull('tripay_reference')
                ->get();

            $countUpdated = 0;
            $ipaymu = new IPaymuController();

            foreach ($pendingTeams as $team) {
                $res = $ipaymu->checkTransactionStatus($team->tripay_reference);

                if ($res && isset($res->Status) && $res->Status == 200 && isset($res->Data)) {
                    $statusIPaymu = (int) ($res->Data->Status ?? 0);
                    
                    if ($statusIPaymu === 1) { // PAID / SUCCESS
                        $currentPaidCount = Team::where('season_id', $team->season_id)
                                            ->where('status', 'PAID')
                                            ->count();

                        if ($currentPaidCount < $team->season->slot) {
                            $team->update([
                                'status' => 'PAID',
                                'status_tripay' => 'SUCCESS',
                                'amount' => $res->Data->Amount ?? $team->season->price,
                            ]);
                            $countUpdated++;

                            // Kirim WhatsApp otomatis ke perwakilan tim saat disinkronkan
                            try {
                                \App\Services\WhatsappService::sendPaidNotification($team);
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Gagal kirim WhatsApp saat sync: ' . $e->getMessage());
                            }

                            // Kirim email notification
                            try {
                                $adminEmail = 'monotp94@gmail.com';
                                \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)->notify(new \App\Notifications\NewRegistration($team));
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Gagal kirim email saat sync: ' . $e->getMessage());
                            }
                        } else {
                            $team->update([
                                'status' => 'FAILED',
                                'status_tripay' => 'SUCCESS',
                            ]);
                            \Illuminate\Support\Facades\Log::warning("OVER-SLOT: Tim {$team->name} bayar tapi slot penuh saat sync.");
                        }
                    }
                }
            }

            return back()->with('success', "Mantap! $countUpdated transaksi iPaymu berhasil disinkronkan.");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal sinkron: " . $e->getMessage());
        }
    }

    public function teams(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $season_id = $request->get('season_id');
        $status = $request->get('status');
        $search = $request->get('search');

        $query = Team::with('season');

        if ($season_id) {
            $query->where('season_id', $season_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('wa_number', 'LIKE', "%$search%")
                  ->orWhere('trx_id', 'LIKE', "%$search%");
            });
        }

        $teams = $query->orderBy('created_at', 'desc')->paginate(25)->appends($request->all());

        // Fetch history for loyalty check for the paginated items
        $wa_numbers = $teams->pluck('wa_number')->unique();
        
        // Get all historical registrations for these WA numbers
        $history = Team::whereIn('wa_number', $wa_numbers)
            ->with('season')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('wa_number');

        foreach ($teams as $team) {
            $teamHistory = $history->get($team->wa_number, collect());
            $team->history = $teamHistory;
            $team->loyalty_count = $teamHistory->count();
        }

        $seasons = Season::orderBy('created_at', 'desc')->get();

        return view('admin.teams_directory', compact('teams', 'seasons', 'season_id', 'status', 'search'));
    }
}