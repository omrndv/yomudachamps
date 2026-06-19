<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\Team;
use App\Models\AdminActivity;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard.home');
        }
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                session()->flash('welcome_alert', 'Selamat datang, ' . $user->name);
            }

            AdminActivity::log('Login admin berhasil');
            return redirect()->route('admin.dashboard.home');
        }

        AdminActivity::log('Gagal login admin: Percobaan masuk dengan username "' . $request->username . '"');
        return back()->with('error', 'Username atau Password salah!');
    }

    public function logout()
    {
        AdminActivity::log('Logout dari panel admin');
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function dashboardHome(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Tentukan rentang tanggal (default 7 hari terakhir)
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        if ($startDateInput && $endDateInput) {
            try {
                $start_date = \Carbon\Carbon::parse($startDateInput)->startOfDay();
                $end_date = \Carbon\Carbon::parse($endDateInput)->endOfDay();
            } catch (\Exception $e) {
                $start_date = now()->subDays(6)->startOfDay();
                $end_date = now()->endOfDay();
            }
        } else {
            $start_date = now()->subDays(6)->startOfDay();
            $end_date = now()->endOfDay();
        }

        // Validasi urutan tanggal
        if ($start_date->gt($end_date)) {
            $temp = $start_date;
            $start_date = $end_date->copy()->startOfDay();
            $end_date = $temp->copy()->endOfDay();
        }

        // Batasi rentang maksimal 90 hari demi performa grafik
        $diffInDays = $start_date->diffInDays($end_date);
        if ($diffInDays > 90) {
            $start_date = $end_date->copy()->subDays(90)->startOfDay();
        }

        // Statistik Global (Keseluruhan)
        $global_registered_teams = Team::count();
        $global_paid_teams = Team::where('status', 'PAID')->count();
        $global_income = Team::where('status', 'PAID')->with('season')->get()->sum(function($t) {
            return $t->season->price ?? 0;
        });

        // Statistik Filtered (Sesuai Rentang Tanggal)
        $total_registered_teams = Team::whereBetween('created_at', [$start_date, $end_date])->count();
        $total_paid_teams = Team::where('status', 'PAID')->whereBetween('created_at', [$start_date, $end_date])->count();
        $total_active_seasons = Season::where('status', 'ACTIVE')->count(); // Tetap global karena status season saat ini
        
        $total_income = Team::where('status', 'PAID')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->with('season')
            ->get()
            ->sum(function($t) {
                return $t->season->price ?? 0;
            });

        // 5 Transaksi Lunas Terbaru (Tetap global atau dalam rentang? Kita buat global karena ini riwayat terbaru sistem)
        $recent_payments = Team::with('season')
            ->where('status', 'PAID')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Semua Season beserta sisa slot terisi
        $seasons = Season::withCount(['teams' => function($q) {
            $q->where('status', 'PAID');
        }])->get()->sort(function ($a, $b) {
            // Tampilkan yang ACTIVE terlebih dahulu
            if ($a->status !== $b->status) {
                return $a->status === 'ACTIVE' ? -1 : 1;
            }
            // Urutkan nama secara alami (natural sorting)
            return strnatcasecmp($a->name, $b->name);
        });

        // Trend Registrasi & Pendapatan Harian Sesuai Rentang Tanggal
        $chart_labels = [];
        $chart_registered = [];
        $chart_paid = [];
        $chart_income = [];

        $currentDate = $start_date->copy();
        while ($currentDate->lte($end_date)) {
            $chart_labels[] = $currentDate->format('d M');
            
            $start = $currentDate->copy()->startOfDay();
            $end = $currentDate->copy()->endOfDay();
            
            $chart_registered[] = Team::whereBetween('created_at', [$start, $end])->count();
            $chart_paid[] = Team::where('status', 'PAID')->whereBetween('created_at', [$start, $end])->count();
            
            $chart_income[] = Team::where('status', 'PAID')
                ->whereBetween('created_at', [$start, $end])
                ->with('season')
                ->get()
                ->sum(function($t) {
                    return $t->season->price ?? 0;
                });

            $currentDate->addDay();
        }

        return view('admin.dashboard_home', compact(
            'total_registered_teams',
            'total_paid_teams',
            'total_active_seasons',
            'total_income',
            'global_registered_teams',
            'global_paid_teams',
            'global_income',
            'recent_payments',
            'seasons',
            'chart_labels',
            'chart_registered',
            'chart_paid',
            'chart_income',
            'start_date',
            'end_date'
        ));
    }

    public function seasons()
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $seasons = Season::withCount('teams')
            ->get()
            ->sort(function ($a, $b) {
                // Tampilkan yang ACTIVE terlebih dahulu
                if ($a->status !== $b->status) {
                    return $a->status === 'ACTIVE' ? -1 : 1;
                }
                // Urutkan nama secara alami (natural sorting)
                return strnatcasecmp($a->name, $b->name);
            });

        return view('admin.seasons', compact('seasons'));
    }

    public function dashboard($season_id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }
    
        $current_season = Season::findOrFail($season_id);
        $teams = Team::where('season_id', $season_id)->get();
        
        $filtered_teams = $teams->map(function ($team) use ($season_id) {
            $history = \App\Models\Team::where('wa_number', $team->wa_number)
                ->where('season_id', '!=', $season_id) // Kecuali season sekarang
                ->where('status', 'PAID') // Hanya yang sudah PAID/bayar
                ->with('season') // Asumsi ada relasi 'season' di Model Team
                ->get();
    
            $team->history = $history;
            $team->is_loyal = $history->count() > 0;
            return $team;
        });
    
        $paid_teams = $filtered_teams->where('status', 'PAID');
        
        // Count paid solo teams
        $solo_teams_count = $paid_teams->where('is_solo_team', true)->count();
        
        // Estimasi Pendapatan Team: (Total Paid Teams - Solo Teams) * Season Price
        $team_income = ($paid_teams->count() - $solo_teams_count) * $current_season->price;
        
        // Estimasi Pendapatan Solo: sum of amount_paid where status = PAID
        $solo_income = \App\Models\SoloPlayer::where('season_id', $season_id)
            ->where('status', 'PAID')
            ->sum('amount_paid');
            
        $total_income = $team_income + $solo_income;
    
        return view('admin.dashboard', compact(
            'current_season',
            'filtered_teams',
            'paid_teams',
            'solo_teams_count',
            'team_income',
            'solo_income',
            'total_income'
        ));
    }

    public function settings()
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $keys = [
            'admin_wa',
            'admin_email',
            'fonnte_token',
            'wa_notification_enabled',
            'wa_template_paid',
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
            'maintenance_secret',
            'log_retention_days'
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
            $publicPath = (is_dir(base_path('../public_html')) && base_path() !== base_path('../public_html')) 
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
            $publicPath = (is_dir(base_path('../public_html')) && base_path() !== base_path('../public_html')) 
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

        AdminActivity::log('Mengubah pengaturan sistem');

        return $response;
    }

    public function bulkStore(Request $request, $season_id)
    {
        $rawData = $request->input('bulk_data');
        $lines = explode("\n", str_replace("\r", "", $rawData));
        $importedCount = 0;
        $lastImportedName = '';
    
        foreach ($lines as $line) {
            if (!trim($line)) continue;
    
            $data = preg_split('/\t+/', $line);
            if (count($data) < 2) {
                $data = preg_split('/\s+(?=\d+$)/', trim($line));
            }
    
            if (count($data) >= 2) {
                $wa_raw = trim($data[1]);
                
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
                } elseif (!str_starts_with($wa_clean, '0') && !str_starts_with($wa_clean, '+')) {
                    // Jika tidak dimulai dengan 0 atau + (misal langsung 853...), tambahkan 0 di depan (asumsi nomor Indo)
                    $wa_clean = '0' . $wa_clean;
                }
    
                $teamName = trim($data[0]);
                Team::create([
                    'season_id' => $season_id,
                    'trx_id'    => 'YMD' . $season_id . '-' . strtoupper(Str::random(4)),
                    'name'      => $teamName,
                    'wa_number' => $wa_clean, 
                    'status'    => 'PAID'
                ]);
                $lastImportedName = $teamName;
                $importedCount++;
            }
        }
    
        $season = Season::find($season_id);
        $seasonName = $season ? $season->name : "ID: $season_id";
    
        if ($importedCount === 1) {
            AdminActivity::log('Menambahkan tim "' . $lastImportedName . '" secara manual untuk season: ' . $seasonName);
        } elseif ($importedCount > 1) {
            AdminActivity::log('Mengimport massal ' . $importedCount . ' tim untuk season: ' . $seasonName);
        }
    
        return back()->with('success', 'Data berhasil diimport ke Database!');
    }

    public function deleteTeam($id)
    {
        if (!Auth::check()) return redirect()->route('admin.login');
        $team = Team::findOrFail($id);
        $teamName = $team->name;
        $team->delete();
        AdminActivity::log('Menghapus tim: ' . $teamName);
        return back()->with('success', 'Tim berhasil dihapus!');
    }

    public function deleteAllTeams($season_id)
    {
        if (!Auth::check()) return redirect()->route('admin.login');
        $season = Season::findOrFail($season_id);
        Team::where('season_id', $season_id)->delete();
        AdminActivity::log('Menghapus semua tim di season: ' . $season->name);
        return back()->with('success', 'Semua tim berhasil dihapus!');
    }

    public function storeSeason(Request $request)
    {
        if (!Auth::check()) return redirect()->route('admin.login');

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
            $destination = storage_path('app/public/posters');
            
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $data['poster'] = $filename;
        }

        $season = Season::create($data);
        AdminActivity::log('Membuat season baru: ' . $season->name);
        return back()->with('success', 'Season baru berhasil dibuat!');
    }

    public function updateSeason(Request $request, $id)
    {
        if (!Auth::check()) return redirect()->route('admin.login');

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
            $destination = storage_path('app/public/posters');

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
        AdminActivity::log('Memperbarui season: ' . $season->name);
        return back()->with('success', 'Season berhasil diperbarui!');
    }

    public function deleteSeason($id)
    {
        if (!Auth::check()) return redirect()->route('admin.login');

        $season = Season::findOrFail($id);
        if ($season->poster) {
            $destination = storage_path('app/public/posters');
            $path = $destination . '/' . $season->poster;
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $seasonName = $season->name;
        $season->delete();
        AdminActivity::log('Menghapus season: ' . $seasonName);
        return back()->with('success', 'Season berhasil dihapus!');
    }

    public function updateTeam(Request $request, $id)
    {
        if (!Auth::check()) return redirect()->route('admin.login');
        $team = Team::findOrFail($id);
        $statusLama = $team->status;
        
        $team->update([
            'name'      => $request->name,
            'wa_number' => $request->wa_number,
            'status'    => $request->status,
        ]);

        AdminActivity::log('Memperbarui data tim: ' . $team->name . ' (Status: ' . $request->status . ')');

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
        if (!Auth::check()) return redirect()->route('admin.login');

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
        if (!Auth::check()) return redirect()->route('admin.login');

        $note = \App\Models\AdminNote::create([
            'title'   => $request->query('title', 'Catatan Baru'),
            'content' => ''
        ]);

        AdminActivity::log('Membuat catatan baru: ' . $note->title);

        return redirect()->route('admin.notes.index', ['id' => $note->id])
                         ->with('success', 'Catatan baru berhasil dibuat!');
    }

    public function updateNotes(Request $request, $id)
    {
        if (!Auth::check()) return response()->json(['error' => 'Unauthorized'], 401);

        $note = \App\Models\AdminNote::findOrFail($id);
        $note->update([
            'title'   => $request->title,
            'content' => $request->content
        ]);

        AdminActivity::log('Memperbarui catatan: ' . $note->title);

        return response()->json([
            'status'     => 'success',
            'updated_at' => $note->updated_at->format('H:i:s, d M Y')
        ]);
    }
    
    public function deleteNote($id)
    {
        if (!Auth::check()) return redirect()->route('admin.login');
        $note = \App\Models\AdminNote::findOrFail($id);
        $noteTitle = $note->title;
        $note->delete();
        AdminActivity::log('Menghapus catatan: ' . $noteTitle);
        return redirect()->route('admin.notes.index')->with('success', 'Catatan berhasil dihapus!');
    }
    
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->team_ids);
        if ($ids) {
            Team::whereIn('id', $ids)->delete();
            AdminActivity::log('Menghapus massal ' . count($ids) . ' tim');
            return back()->with('success', count($ids) . ' tim berhasil dihapus.');
        }
        return back()->with('error', 'Tidak ada tim yang dipilih.');
    }
    
        // FUNGSI UTAMA VIEW (Kembali ke DB Lokal agar Filter & Search Jalan)
    public function paymentHistory(Request $request)
    {
        if (!Auth::check()) return redirect()->route('admin.login');
    
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
        if (!Auth::check()) return redirect()->route('admin.login');

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

    public function backupDatabase()
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        try {
            $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
            $dbName = config('database.connections.mysql.database');
            $dbNameKey = 'Tables_in_' . $dbName;
            
            $sqlDump = "-- Yomuda Championship Database Backup\n";
            $sqlDump .= "-- Generated: " . now()->toDateTimeString() . " WIB\n";
            $sqlDump .= "-- Project URL: " . url('/') . "\n\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {
                // Handle different keys representing the table name
                $tableObjArray = (array)$tableObj;
                $tableName = $tableObj->$dbNameKey ?? reset($tableObjArray);
                
                if (empty($tableName)) continue;

                // Get CREATE TABLE
                $createTable = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlDump .= ((array)$createTable[0])['Create Table'] . ";\n\n";
                
                // Get rows
                $rows = \Illuminate\Support\Facades\DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $escapedValues = array_map(function($val) {
                        if ($val === null) return 'NULL';
                        return \Illuminate\Support\Facades\DB::getPdo()->quote($val);
                    }, $rowArray);
                    
                    $columns = array_keys($rowArray);
                    $columnsStr = implode('`, `', $columns);
                    $valuesStr = implode(', ', $escapedValues);
                    
                    $sqlDump .= "INSERT INTO `{$tableName}` (`{$columnsStr}`) VALUES ({$valuesStr});\n";
                }
                $sqlDump .= "\n";
            }
            
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            $filename = 'backup_yomudachamps_' . now()->format('Y-m-d_H-i-s') . '.sql';
            
            AdminActivity::log('Melakukan backup database');

            return response($sqlDump)
                ->header('Content-Type', 'application/sql')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal backup database: ' . $e->getMessage());
        }
    }

    public function activityLog(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Run log rotation/cleanup dynamically based on setting
        $retentionDays = (int) \App\Models\Setting::getVal('log_retention_days', 15);
        if ($retentionDays > 0) {
            AdminActivity::where('created_at', '<', now()->subDays($retentionDays))->delete();
        }

        $type = $request->query('type', 'login'); // Default is 'login'

        $query = AdminActivity::with('user');

        if ($type === 'login') {
            $query->where(function($q) {
                $q->where('activity', 'like', '%login%')
                  ->orWhere('activity', 'like', '%logout%')
                  ->orWhere('activity', 'like', '%masuk%')
                  ->orWhere('activity', 'like', '%keluar%');
            });
        } elseif ($type === 'tambah') {
            $query->where(function($q) {
                $q->where('activity', 'like', '%tambah%')
                  ->orWhere('activity', 'like', '%buat%')
                  ->orWhere('activity', 'like', '%store%')
                  ->orWhere('activity', 'like', '%create%')
                  ->orWhere('activity', 'like', '%import%');
            });
        } elseif ($type === 'ubah') {
            $query->where(function($q) {
                $q->where('activity', 'like', '%ubah%')
                  ->orWhere('activity', 'like', '%perbarui%')
                  ->orWhere('activity', 'like', '%update%')
                  ->orWhere('activity', 'like', '%edit%')
                  ->orWhere('activity', 'like', '%reorder%');
            });
        } elseif ($type === 'hapus') {
            $query->where(function($q) {
                $q->where('activity', 'like', '%hapus%')
                  ->orWhere('activity', 'like', '%delete%')
                  ->orWhere('activity', 'like', '%remove%');
            });
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        return view('admin.activity_log', compact('activities', 'type'));
    }

    public function faqs()
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $faqs = Faq::orderBy('order', 'asc')->get();
        return view('admin.faqs', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'order' => 'integer'
        ]);

        $faq = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0
        ]);

        AdminActivity::log('Menambahkan FAQ baru: "' . Str::limit($faq->question, 40) . '"');

        return back()->with('success', 'FAQ baru berhasil ditambahkan!');
    }

    public function updateFaq(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'order' => 'integer'
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'order' => $request->order ?? 0
        ]);

        AdminActivity::log('Memperbarui FAQ: "' . Str::limit($faq->question, 40) . '"');

        return back()->with('success', 'FAQ berhasil diperbarui!');
    }

    public function deleteFaq($id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $faq = Faq::findOrFail($id);
        $faqQuestion = $faq->question;
        $faq->delete();

        AdminActivity::log('Menghapus FAQ: "' . Str::limit($faqQuestion, 40) . '"');

        return back()->with('success', 'FAQ berhasil dihapus!');
    }

    public function reorderFaq(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'direction' => 'required|in:up,down'
        ]);

        $faq = Faq::findOrFail($id);
        $direction = $request->direction;

        // Normalize all orders sequentially to ensure they are clean (1, 2, 3...)
        $faqs = Faq::orderBy('order', 'asc')->orderBy('id', 'asc')->get();
        foreach ($faqs as $index => $item) {
            $item->order = $index + 1;
            $item->save();
        }

        // Reload current FAQ after normalization
        $faq = Faq::findOrFail($id);
        $currentOrder = $faq->order;

        if ($direction === 'up') {
            $targetFaq = Faq::where('order', $currentOrder - 1)->first();
            if ($targetFaq) {
                $targetFaq->order = $currentOrder;
                $targetFaq->save();
                
                $faq->order = $currentOrder - 1;
                $faq->save();
            }
        } elseif ($direction === 'down') {
            $targetFaq = Faq::where('order', $currentOrder + 1)->first();
            if ($targetFaq) {
                $targetFaq->order = $currentOrder;
                $targetFaq->save();
                
                $faq->order = $currentOrder + 1;
                $faq->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function adminList()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }

        $admins = User::where('role', 'admin')->orderBy('name', 'asc')->get();
        return view('admin.manage_admins', compact('admins'));
    }

    public function storeAdmin(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        AdminActivity::log('Membuat akun admin baru: ' . $admin->username);

        return back()->with('success', 'Akun admin berhasil ditambahkan!');
    }

    public function updateAdmin(Request $request, $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }

        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        AdminActivity::log('Memperbarui akun admin: ' . $admin->username);

        return back()->with('success', 'Akun admin berhasil diperbarui!');
    }

    public function deleteAdmin($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }

        $admin = User::findOrFail($id);
        $adminUsername = $admin->username;
        $admin->delete();

        AdminActivity::log('Menghapus akun admin: ' . $adminUsername);

        return back()->with('success', 'Akun admin berhasil dihapus!');
    }

    public function soloMatchmaker($season_id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $current_season = Season::findOrFail($season_id);
        
        // Fetch players for the current season, grouped or unmatched
        $unmatched_players = \App\Models\SoloPlayer::where('season_id', $season_id)
            ->whereNull('team_id')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $matched_players = \App\Models\SoloPlayer::where('season_id', $season_id)
            ->whereNotNull('team_id')
            ->with('team')
            ->orderBy('created_at', 'desc')
            ->get();

        $ranks = ['Epic', 'Legend', 'Mythic', 'Honor', 'Glory', 'Immortal'];
        $roles = ['Roamer', 'Gold Lane', 'Mid Lane', 'Exp Lane', 'Jungler'];

        return view('admin.solo_matchmaker', compact(
            'current_season',
            'unmatched_players',
            'matched_players',
            'ranks',
            'roles'
        ));
    }

    public function storeSoloPlayer(Request $request, $season_id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'wa_number' => 'required|string|max:20',
            'role' => 'required|string',
            'rank' => 'required|string',
            'status' => 'required|in:PENDING,PAID',
            'amount_paid' => 'required|integer|min:0',
        ]);

        \App\Models\SoloPlayer::create([
            'season_id' => $season_id,
            'wa_number' => $request->wa_number,
            'role' => $request->role,
            'rank' => $request->rank,
            'status' => $request->status,
            'amount_paid' => $request->amount_paid,
        ]);

        AdminActivity::log('Menambahkan solo player baru dengan nomor WA: ' . $request->wa_number);

        return back()->with('success', 'Solo player berhasil ditambahkan!');
    }

    public function bulkStoreSolo(Request $request, $season_id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'bulk_data' => 'required|string',
            'default_amount' => 'required|integer|min:0',
            'default_status' => 'required|in:PENDING,PAID',
        ]);

        $lines = explode("\n", $request->bulk_data);
        $addedCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Expected format: WA | Role | Rank
            // Allow comma or pipe or tab separation
            $delimiters = ['|', ';', ','];
            $parts = [];
            foreach ($delimiters as $delim) {
                if (strpos($line, $delim) !== false) {
                    $parts = array_map('trim', explode($delim, $line));
                    break;
                }
            }

            if (empty($parts)) {
                // Try split by whitespace/tab if no separator found
                $parts = array_map('trim', preg_split('/\s{2,}/', $line));
            }

            if (count($parts) >= 1) {
                $wa = $parts[0];
                $role = isset($parts[1]) ? $parts[1] : 'Roamer';
                $rank = isset($parts[2]) ? $parts[2] : 'Legend';

                \App\Models\SoloPlayer::create([
                    'season_id' => $season_id,
                    'wa_number' => $wa,
                    'role' => $role,
                    'rank' => $rank,
                    'status' => $request->default_status,
                    'amount_paid' => $request->default_amount,
                ]);
                $addedCount++;
            }
        }

        AdminActivity::log('Berhasil mengimpor massal ' . $addedCount . ' solo player');

        return back()->with('success', "Berhasil menambahkan {$addedCount} solo player!");
    }

    public function groupSoloPlayers(Request $request, $season_id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'player_ids' => 'required|array|size:5',
            'team_name' => 'required|string|max:255',
        ]);

        $players = \App\Models\SoloPlayer::whereIn('id', $request->player_ids)
            ->whereNull('team_id')
            ->get();

        if ($players->count() !== 5) {
            return back()->with('error', 'Silakan pilih tepat 5 player solo yang belum tergabung dalam tim.');
        }

        // Create the Team
        $team = Team::create([
            'season_id' => $season_id,
            'trx_id' => 'SOLO-' . strtoupper(Str::random(8)),
            'name' => $request->team_name,
            'wa_number' => $players->first()->wa_number, // Default to first player's WhatsApp
            'status' => 'PAID', // Formed teams are automatically marked as PAID
            'is_solo_team' => true,
        ]);

        // Link solo players to team
        foreach ($players as $player) {
            $player->team_id = $team->id;
            $player->status = 'PAID'; // Ensure status is updated to PAID when grouped
            $player->save();
        }

        AdminActivity::log('Membentuk tim solo "' . $team->name . '" berisi nomor WA: ' . $players->pluck('wa_number')->implode(', '));

        return back()->with('success', 'Tim solo berhasil dibentuk!');
    }

    public function deleteSoloPlayer($id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $player = \App\Models\SoloPlayer::findOrFail($id);
        $wa = $player->wa_number;
        $player->delete();

        AdminActivity::log('Menghapus solo player dengan nomor WA: ' . $wa);

        return back()->with('success', 'Solo player berhasil dihapus!');
    }
}