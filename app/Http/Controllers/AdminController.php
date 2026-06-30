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

    public function checkNewPayments()
    {
        $latestPaid = Team::where('status', 'PAID')
            ->where('updated_at', '>=', now()->subMinutes(2))
            ->orderBy('updated_at', 'desc')
            ->first(['id', 'name', 'trx_id', 'updated_at']);

        return response()->json([
            'success' => true,
            'latest_paid' => $latestPaid
        ]);
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
        
        // Pemasukan Otomatis via TriPay
        $tripay_teams = $paid_teams->where('is_solo_team', false)->whereNotNull('tripay_reference');
        $tripay_income = $tripay_teams->sum(function($t) use ($current_season) {
            return $t->amount && $t->amount > 0 ? $t->amount : $current_season->price;
        });

        // Pemasukan Manual / Bulk Add oleh Admin (Kecualikan tim placeholder YMD)
        $manual_teams = $paid_teams->where('is_solo_team', false)
            ->whereNull('tripay_reference')
            ->filter(function($t) {
                return !str_starts_with(strtolower($t->name), 'ymd');
            });
            
        $manual_income = $manual_teams->sum(function($t) use ($current_season) {
            return $t->amount && $t->amount > 0 ? $t->amount : $current_season->price;
        });
        
        $team_income = $tripay_income + $manual_income;
        
        // Estimasi Pendapatan Solo: sum of amount_paid where status = PAID
        $solo_income = \App\Models\SoloPlayer::where('season_id', $season_id)
            ->where('status', 'PAID')
            ->sum('amount_paid');
            
        $total_income = $team_income + $solo_income;

        // Catatan Keuangan Pemasukan & Pengeluaran Season
        $finances = \App\Models\SeasonFinance::where('season_id', $season_id)->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        $additional_income = $finances->where('type', 'INCOME')->sum('amount');
        $total_expense = $finances->where('type', 'EXPENSE')->sum('amount');
        $net_income = $total_income + $additional_income - $total_expense;
    
        return view('admin.dashboard', compact(
            'current_season',
            'filtered_teams',
            'paid_teams',
            'solo_teams_count',
            'team_income',
            'tripay_income',
            'manual_income',
            'solo_income',
            'total_income',
            'finances',
            'additional_income',
            'total_expense',
            'net_income'
        ));
    }

    public function financeIndex($season_id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $current_season = Season::findOrFail($season_id);
        $teams = Team::where('season_id', $season_id)->get();
        $paid_teams = $teams->where('status', 'PAID');
        $solo_teams_count = $paid_teams->where('is_solo_team', true)->count();
        
        // Pemasukan Otomatis via TriPay
        $tripay_teams = $paid_teams->where('is_solo_team', false)->whereNotNull('tripay_reference');
        $tripay_income = $tripay_teams->sum(function($t) use ($current_season) {
            return $t->amount && $t->amount > 0 ? $t->amount : $current_season->price;
        });

        // Pemasukan Manual / Bulk Add oleh Admin (Kecualikan tim placeholder YMD)
        $manual_teams = $paid_teams->where('is_solo_team', false)
            ->whereNull('tripay_reference')
            ->filter(function($t) {
                return !str_starts_with(strtolower($t->name), 'ymd');
            });
            
        $manual_income = $manual_teams->sum(function($t) use ($current_season) {
            return $t->amount && $t->amount > 0 ? $t->amount : $current_season->price;
        });
        
        $team_income = $tripay_income + $manual_income;
        
        $solo_income = \App\Models\SoloPlayer::where('season_id', $season_id)
            ->where('status', 'PAID')
            ->sum('amount_paid');
            
        $total_income = $team_income + $solo_income;

        $finances = \App\Models\SeasonFinance::where('season_id', $season_id)->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();
        $additional_income = $finances->where('type', 'INCOME')->sum('amount');
        $total_expense = $finances->where('type', 'EXPENSE')->sum('amount');
        $net_income = $total_income + $additional_income - $total_expense;

        // Hitung Keuntungan & Jumlah Pembelian Slot YMD
        $ymd_finances = $finances->filter(function($f) {
            return str_contains(strtolower($f->title), 'penjualan slot ymd');
        });
        $ymd_slots_count = $ymd_finances->count();
        $ymd_slots_income = $ymd_finances->sum('amount');

        return view('admin.finance', compact(
            'current_season',
            'team_income',
            'tripay_income',
            'manual_income',
            'solo_income',
            'total_income',
            'finances',
            'additional_income',
            'total_expense',
            'net_income',
            'ymd_slots_count',
            'ymd_slots_income'
        ));
    }

    public function storeFinance(Request $request, $season_id)
    {
        if (!Auth::check()) return redirect()->route('admin.login');

        $request->validate([
            'type' => 'required|in:INCOME,EXPENSE',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'nullable|date'
        ]);

        \App\Models\SeasonFinance::create([
            'season_id' => $season_id,
            'type' => $request->type,
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date ?? now()->toDateString()
        ]);

        $typeLabel = $request->type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran';
        $season = Season::find($season_id);
        $seasonName = $season ? $season->name : "ID: $season_id";
        AdminActivity::log("Menambahkan catatan keuangan ($typeLabel) '{$request->title}' sebesar Rp " . number_format($request->amount) . " untuk season: " . $seasonName);

        return back()->with('success', 'Catatan keuangan berhasil ditambahkan!');
    }

    public function deleteFinance($season_id, $id)
    {
        if (!Auth::check()) return redirect()->route('admin.login');

        $finance = \App\Models\SeasonFinance::where('season_id', $season_id)->findOrFail($id);
        $title = $finance->title;
        $amount = $finance->amount;
        $typeLabel = $finance->type === 'INCOME' ? 'Pemasukan' : 'Pengeluaran';
        
        $finance->delete();

        $season = Season::find($season_id);
        $seasonName = $season ? $season->name : "ID: $season_id";
        AdminActivity::log("Menghapus catatan keuangan ($typeLabel) '{$title}' sebesar Rp " . number_format($amount) . " untuk season: " . $seasonName);

        return back()->with('success', 'Catatan keuangan berhasil dihapus!');
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
            'social_instagram',
            'social_tiktok',
            'social_youtube',
            'maintenance_secret',
            'log_retention_days',
            'global_rules_link'
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

        // Handle rules PDF file upload
        if ($request->hasFile('rules_file')) {
            $rulesPath = public_path('rules');
            if (!file_exists($rulesPath)) {
                mkdir($rulesPath, 0755, true);
            }
            $file = $request->file('rules_file');
            $file->move($rulesPath, 'rules-turnamen.pdf');
            
            \App\Models\Setting::updateOrCreate(
                ['key' => 'global_rules_link'],
                ['value' => asset('rules/rules-turnamen.pdf')]
            );
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = public_path('images');
            if (!file_exists($logoPath)) {
                mkdir($logoPath, 0755, true);
            }
            $file = $request->file('logo');
            $file->move($logoPath, 'logo-yomuda.png');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $file->move(public_path(), 'favicon.ico');
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
            'rules_link' => $request->rules_link,
            'schedule_info' => $request->schedule_info,
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
            'rules_link' => $request->rules_link,
            'schedule_info' => $request->schedule_info,
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
    
    // FUNGSI UTAMA VIEW (Mengambil Data Langsung Secara Live dari API TriPay)
    public function paymentHistory(Request $request)
    {
        if (!Auth::check()) return redirect()->route('admin.login');
    
        $page = $request->get('page', 1);
        $status = $request->get('status');
        $search = $request->get('search');
    
        // Optimasi Pencarian Pintar: Terjemahkan nama tim / nomor WA lokal ke format Ref/Invoice TriPay
        $apiSearch = $search;
        if ($search) {
            $searchUpper = strtoupper(trim($search));
            
            // 1. Jika pencarian adalah TriPay Reference (misal diawali T)
            if (str_starts_with($searchUpper, 'T') && strlen($searchUpper) > 5) {
                $apiSearch = $searchUpper;
            }
            // 2. Jika pencarian adalah Invoice lokal (diawali YMD)
            elseif (str_starts_with($searchUpper, 'YMD')) {
                $apiSearch = $searchUpper;
            }
            // 3. Jika pencarian adalah Nama Tim atau Nomor WA, cari di DB lokal untuk mendapatkan trx_id (merchant_ref)
            else {
                $localTeam = \App\Models\Team::where('name', 'LIKE', "%{$search}%")
                    ->orWhere('wa_number', 'LIKE', "%{$search}%")
                    ->first();
                
                if ($localTeam) {
                    $apiSearch = $localTeam->trx_id;
                }
            }
        }
    
        $tripay = new TripayController();
        $startTime = microtime(true);
        $response = $tripay->getTransactionsList($page, 20, $status, $apiSearch);
        $endTime = microtime(true);
        
        $apiLatency = round(($endTime - $startTime) * 1000); // dalam milidetik
        $apiStatus = ($response && isset($response->success) && $response->success) ? 'Connected' : 'Disconnected';
    
        $payments = [];
        $pagination = null;
        $total_trx = 0;
        $total_cuan = 0; // Pendapatan bersih di halaman ini
        $total_fee = 0;  // Total fee TriPay di halaman ini
    
        if ($response && isset($response->success) && $response->success) {
            $payments = $response->data ?? [];
            $pagination = $response->pagination ?? null;
            $total_trx = $pagination->total ?? count($payments);
    
            foreach ($payments as $pay) {
                if (strtoupper($pay->status ?? '') === 'PAID') {
                    $total_cuan += $pay->amount_received ?? 0;
                    $total_fee += $pay->total_fee ?? 0;
                }
            }
        }
    
        $tripayMode = \App\Models\Setting::getVal('tripay_mode', env('TRIPAY_MODE', 'sandbox'));
    
        return view('admin.payment_history', compact(
            'payments',
            'pagination',
            'total_trx',
            'total_cuan',
            'total_fee',
            'status',
            'search',
            'tripayMode',
            'apiLatency',
            'apiStatus'
        ));
    }
    
    // FUNGSI SINKRONISASI (MAGIC BUTTON)
    public function syncPayments()
    {
        try {
            $pendingTeams = Team::where('status', 'PENDING')
                ->whereNotNull('tripay_reference')
                ->get();

            $countUpdated = 0;
            $tripay = new TripayController();

            foreach ($pendingTeams as $team) {
                $res = $tripay->getDetailTransaction($team->tripay_reference);

                if ($res) {
                    $statusTriPay = strtoupper((string) ($res->status ?? ''));
                    
                    if ($statusTriPay === 'PAID') { // PAID / SUCCESS
                        $currentPaidCount = Team::where('season_id', $team->season_id)
                                            ->where('status', 'PAID')
                                            ->count();

                        if ($currentPaidCount < $team->season->slot) {
                            $team->update([
                                'status' => 'PAID',
                                'status_tripay' => 'PAID',
                                'amount' => $res->amount_received ?? $team->season->price,
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
                                'status_tripay' => 'PAID',
                            ]);
                            \Illuminate\Support\Facades\Log::warning("OVER-SLOT: Tim {$team->name} bayar tapi slot penuh saat sync.");
                        }
                    }
                }
            }

            return back()->with('success', "Mantap! $countUpdated transaksi TriPay berhasil disinkronkan.");
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
        if (!Auth::user()->hasPermission('manage')) {
            abort(403, 'Unauthorized');
        }

        $admins = User::where('role', 'admin')->orderBy('name', 'asc')->get();
        return view('admin.manage_admins', compact('admins'));
    }

    public function storeAdmin(Request $request)
    {
        if (!Auth::user()->hasPermission('manage')) {
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
            'permissions' => [
                "dashboard",
                "seasons",
                "notes",
                "activity_log",
                "faqs",
                "finance",
                "solo_matchmaker"
            ],
        ]);

        AdminActivity::log('Membuat akun admin baru: ' . $admin->username);

        return back()->with('success', 'Akun admin berhasil ditambahkan!');
    }

    public function updateAdmin(Request $request, $id)
    {
        if (!Auth::user()->hasPermission('manage')) {
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
        if (!Auth::user()->hasPermission('manage')) {
            abort(403, 'Unauthorized');
        }

        $admin = User::findOrFail($id);
        $adminUsername = $admin->username;
        $admin->delete();

        AdminActivity::log('Menghapus akun admin: ' . $adminUsername);

        return back()->with('success', 'Akun admin berhasil dihapus!');
    }

    public function togglePermission(Request $request)
    {
        if (!Auth::user()->hasPermission('manage')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'admin_id' => 'required|exists:users,id',
            'permission' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $admin = User::findOrFail($request->admin_id);

        if ($admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya akun admin yang bisa diubah izinnya.'], 400);
        }

        $currentPermissions = $admin->permissions;
        if (!is_array($currentPermissions)) {
            $currentPermissions = json_decode($currentPermissions, true) ?: [];
        }

        $permission = $request->permission;
        $status = (bool) $request->status;

        if ($status) {
            if (!in_array($permission, $currentPermissions)) {
                $currentPermissions[] = $permission;
            }
        } else {
            $currentPermissions = array_values(array_diff($currentPermissions, [$permission]));
        }

        $admin->permissions = $currentPermissions;
        $admin->save();

        AdminActivity::log('Mengubah izin ' . $permission . ' untuk admin ' . $admin->username . ' menjadi ' . ($status ? 'AKTIF' : 'NONAKTIF'));

        return response()->json([
            'success' => true, 
            'permissions' => $currentPermissions,
            'message' => 'Izin berhasil diperbarui'
        ]);
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

        // Fetch all solo teams for this season (some might be empty)
        $solo_teams = Team::where('season_id', $season_id)
            ->where('is_solo_team', true)
            ->with('season')
            ->get()
            ->map(function ($team) {
                // Attach players assigned to this team
                $team->players = \App\Models\SoloPlayer::where('team_id', $team->id)->get();
                return $team;
            });

        $ranks = ['Epic', 'Legend', 'Mythic', 'Honor', 'Glory', 'Immortal'];
        $roles = ['Roamer', 'Gold Lane', 'Mid Lane', 'Exp Lane', 'Jungler'];

        return view('admin.solo_matchmaker', compact(
            'current_season',
            'unmatched_players',
            'matched_players',
            'solo_teams',
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

    public function createEmptySoloTeam(Request $request, $season_id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'team_name' => 'required|string|max:255',
        ]);

        $team = Team::create([
            'season_id' => $season_id,
            'trx_id' => 'SOLO-' . strtoupper(Str::random(8)),
            'name' => $request->team_name,
            'wa_number' => '-', 
            'status' => 'PAID',
            'is_solo_team' => true,
        ]);

        AdminActivity::log('Membuat tim solo kosong baru: ' . $team->name);

        return back()->with('success', 'Tim solo kosong berhasil dibuat!');
    }

    public function updatePlayerTeam(Request $request, $season_id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'player_id' => 'required|integer',
            'team_id' => 'nullable|integer', // null means unmatched/move back to pool
        ]);

        $player = \App\Models\SoloPlayer::where('season_id', $season_id)->findOrFail($request->player_id);
        
        // Find duo/trio mates by matching wa_number and same season
        $playersToMove = \App\Models\SoloPlayer::where('season_id', $season_id)
            ->where('wa_number', $player->wa_number)
            ->get();

        $teamName = 'Pool Unmatched';
        if ($request->team_id) {
            $team = Team::where('season_id', $season_id)->findOrFail($request->team_id);
            $teamName = $team->name;

            // Fetch players currently assigned to this team (excluding players who are moving)
            $movingIds = $playersToMove->pluck('id')->toArray();
            $currentTeamPlayers = \App\Models\SoloPlayer::where('team_id', $team->id)
                ->whereNotIn('id', $movingIds)
                ->get();

            // 1. Validation: Team size check (max 5 players)
            if (($currentTeamPlayers->count() + $playersToMove->count()) > 5) {
                return response()->json([
                    'success' => false,
                    'message' => "Tim '{$team->name}' sudah penuh atau slot tidak mencukupi untuk memasukkan kelompok ini (maksimal 5 player)."
                ], 422);
            }

            // 2. Validation: Duplicate role check
            // Roles already present in target team
            $existingRoles = $currentTeamPlayers->pluck('role')->map(function($r) {
                return strtolower(trim($r));
            })->toArray();

            // Check if any moving player has a duplicate role with current team players
            // Or if there are duplicate roles inside the moving duo/trio itself
            $movingRoles = [];
            foreach ($playersToMove as $p) {
                $roleNormalized = strtolower(trim($p->role));
                
                // Compare with current team members
                if (in_array($roleNormalized, $existingRoles)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Gagal! Role '{$p->role}' sudah terisi di dalam tim '{$team->name}'."
                    ], 422);
                }

                // Compare within the moving group
                if (in_array($roleNormalized, $movingRoles)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Gagal! Ada duplikasi role '{$p->role}' di dalam kelompok Duo/Trio yang ingin dimasukkan."
                    ], 422);
                }

                $movingRoles[] = $roleNormalized;
            }
            
            // Auto update team's WhatsApp number to the first player's WhatsApp if it is currently '-'
            if ($team->wa_number === '-') {
                $team->wa_number = $player->wa_number;
                $team->save();
            }
        }

        foreach ($playersToMove as $p) {
            $p->team_id = $request->team_id;
            if ($request->team_id) {
                $p->status = 'PAID';
            }
            $p->save();
        }

        AdminActivity::log("Memindahkan player(s) dengan WA {$player->wa_number} ke tim: {$teamName}");

        return response()->json([
            'success' => true, 
            'message' => 'Team updated successfully',
            'moved_ids' => $playersToMove->pluck('id')
        ]);
    }

    public function updateSoloPlayer(Request $request, $id)
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

        $player = \App\Models\SoloPlayer::findOrFail($id);
        
        $player->update([
            'wa_number' => $request->wa_number,
            'role' => $request->role,
            'rank' => $request->rank,
            'status' => $request->status,
            'amount_paid' => $request->amount_paid,
        ]);

        AdminActivity::log('Memperbarui data solo player dengan WA: ' . $player->wa_number);

        return back()->with('success', 'Data player berhasil diperbarui!');
    }

    public function updateSoloTeamDetails(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'wa_number' => 'required|string|max:20',
        ]);

        $team = Team::findOrFail($id);
        $oldName = $team->name;
        
        $team->update([
            'name' => $request->name,
            'wa_number' => $request->wa_number,
        ]);

        AdminActivity::log("Memperbarui detail tim solo: '{$oldName}' menjadi '{$team->name}' (WA: {$team->wa_number})");

        return back()->with('success', 'Detail tim berhasil diperbarui!');
    }

    public function suggestTeams($season_id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $unmatched = \App\Models\SoloPlayer::where('season_id', $season_id)
            ->whereNull('team_id')
            ->get();

        if ($unmatched->count() < 5) {
            return back()->with('error', 'Jumlah player di pool unmatched kurang dari 5, tidak dapat membentuk tim.');
        }

        // Phase 1: Try to fill empty slots in existing incomplete solo teams
        $existingTeams = Team::where('season_id', $season_id)
            ->where('is_solo_team', true)
            ->get();

        foreach ($existingTeams as $team) {
            $assignedPlayers = \App\Models\SoloPlayer::where('team_id', $team->id)->get();
            $slotsAvailable = 5 - $assignedPlayers->count();
            if ($slotsAvailable <= 0) {
                continue; // team is already full
            }

            // Get roles currently filled in this team
            $filledRoles = $assignedPlayers->pluck('role')->map(function($r) {
                return strtolower(trim($r));
            })->toArray();

            // Fetch unmatched players and try to slot them in if they fill a missing role
            $remainingUnmatched = \App\Models\SoloPlayer::where('season_id', $season_id)
                ->whereNull('team_id')
                ->get();

            // Group remaining unmatched by WA to respect duo/trio
            $unmatchedGroups = $remainingUnmatched->groupBy('wa_number');

            foreach ($unmatchedGroups as $wa => $group) {
                $groupSize = $group->count();
                if ($groupSize > $slotsAvailable) {
                    continue; // group is too big for the remaining slots
                }

                // Check for role conflicts between group and team, and within group
                $conflict = false;
                $groupRoles = [];
                foreach ($group as $gp) {
                    $gRole = strtolower(trim($gp->role));
                    if (in_array($gRole, $filledRoles) || in_array($gRole, $groupRoles)) {
                        $conflict = true;
                        break;
                    }
                    $groupRoles[] = $gRole;
                }

                if (!$conflict) {
                    // Assign this group to the team!
                    foreach ($group as $gp) {
                        $gp->team_id = $team->id;
                        $gp->status = 'PAID';
                        $gp->save();
                    }
                    // Update variables for next checks
                    $slotsAvailable -= $groupSize;
                    $filledRoles = array_merge($filledRoles, $groupRoles);
                    
                    // Auto-update team representative WA if not set
                    if ($team->wa_number === '-' || empty($team->wa_number)) {
                        $team->wa_number = $wa;
                        $team->save();
                    }

                    if ($slotsAvailable <= 0) {
                        break; // team is now full
                    }
                }
            }
        }

        // Fetch fresh unmatched list for Phase 2 (forming new teams)
        $unmatched = \App\Models\SoloPlayer::where('season_id', $season_id)
            ->whereNull('team_id')
            ->get();

        if ($unmatched->count() < 5) {
            AdminActivity::log("Algoritma Matchmaker selesai memproses pengisian slot kosong.");
            return back()->with('success', "Proses pengisian slot kosong selesai!");
        }

        // Group players by wa_number to respect duo/trio bindings
        $groups = $unmatched->groupBy('wa_number');

        // Separate groups by size
        $trios = [];
        $duos = [];
        $solos = [];

        foreach ($groups as $wa => $group) {
            $count = $group->count();
            if ($count === 3) {
                $trios[] = $group->all();
            } elseif ($count === 2) {
                $duos[] = $group->all();
            } elseif ($count === 1) {
                $solos[] = $group->first();
            } else {
                // If group is larger than 3, split it into smaller pieces for safety
                foreach ($group as $p) {
                    $solos[] = $p;
                }
            }
        }

        $suggestedTeamsCount = 0;

        // Try to form teams of 5 using:
        // Option A: 1 Trio + 1 Duo
        // Option B: 1 Trio + 2 Solos
        // Option C: 2 Duos + 1 Solo
        // Option D: 1 Duo + 3 Solos
        // Option E: 5 Solos

        $usedTrioIndices = [];
        $usedDuoIndices = [];
        $usedSoloIds = [];

        // Helper to check role conflict in a team structure
        $hasRoleConflict = function($players) {
            $roles = [];
            foreach ($players as $p) {
                $role = strtolower(trim($p->role));
                if (in_array($role, $roles)) {
                    return true;
                }
                $roles[] = $role;
            }
            return false;
        };

        $createSuggestedTeam = function($players) use ($season_id, &$suggestedTeamsCount) {
            // Find next team letter index or generic suffix
            $count = Team::where('season_id', $season_id)->where('is_solo_team', true)->count() + 1;
            $team = Team::create([
                'season_id' => $season_id,
                'trx_id' => 'SOLO-' . strtoupper(Str::random(8)),
                'name' => 'Solo Team ' . chr(64 + ($count > 26 ? 26 : $count)),
                'wa_number' => $players[0]->wa_number,
                'status' => 'PAID',
                'is_solo_team' => true,
            ]);

            foreach ($players as $p) {
                $p->team_id = $team->id;
                $p->status = 'PAID';
                $p->save();
            }
            $suggestedTeamsCount++;
        };

        // 1. Try Trio + Duo
        for ($i = 0; $i < count($trios); $i++) {
            if (in_array($i, $usedTrioIndices)) continue;
            for ($j = 0; $j < count($duos); $j++) {
                if (in_array($j, $usedDuoIndices)) continue;

                $candidate = array_merge($trios[$i], $duos[$j]);
                if (!$hasRoleConflict($candidate)) {
                    $createSuggestedTeam($candidate);
                    $usedTrioIndices[] = $i;
                    $usedDuoIndices[] = $j;
                    break;
                }
            }
        }

        // 2. Try Trio + 2 Solos
        for ($i = 0; $i < count($trios); $i++) {
            if (in_array($i, $usedTrioIndices)) continue;
            
            // Find 2 solos
            $candidateSolos = [];
            foreach ($solos as $s) {
                if (in_array($s->id, $usedSoloIds)) continue;
                $candidateSolos[] = $s;
            }

            // Look for combinations of 2 solos
            $found = false;
            for ($x = 0; $x < count($candidateSolos); $x++) {
                for ($y = $x + 1; $y < count($candidateSolos); $y++) {
                    $candidate = array_merge($trios[$i], [$candidateSolos[$x], $candidateSolos[$y]]);
                    if (!$hasRoleConflict($candidate)) {
                        $createSuggestedTeam($candidate);
                        $usedTrioIndices[] = $i;
                        $usedSoloIds[] = $candidateSolos[$x]->id;
                        $usedSoloIds[] = $candidateSolos[$y]->id;
                        $found = true;
                        break 2;
                    }
                }
            }
        }

        // 3. Try 2 Duos + 1 Solo
        for ($i = 0; $i < count($duos); $i++) {
            if (in_array($i, $usedDuoIndices)) continue;
            for ($j = $i + 1; $j < count($duos); $j++) {
                if (in_array($j, $usedDuoIndices)) continue;

                // Find 1 solo
                foreach ($solos as $s) {
                    if (in_array($s->id, $usedSoloIds)) continue;
                    
                    $candidate = array_merge($duos[$i], $duos[$j], [$s]);
                    if (!$hasRoleConflict($candidate)) {
                        $createSuggestedTeam($candidate);
                        $usedDuoIndices[] = $i;
                        $usedDuoIndices[] = $j;
                        $usedSoloIds[] = $s->id;
                        break 2;
                    }
                }
            }
        }

        // 4. Try 1 Duo + 3 Solos
        for ($i = 0; $i < count($duos); $i++) {
            if (in_array($i, $usedDuoIndices)) continue;

            $candidateSolos = [];
            foreach ($solos as $s) {
                if (in_array($s->id, $usedSoloIds)) continue;
                $candidateSolos[] = $s;
            }

            // Try to find 3 non-conflicting solos
            $solosCount = count($candidateSolos);
            $found = false;
            for ($x = 0; $x < $solosCount; $x++) {
                for ($y = $x + 1; $y < $solosCount; $y++) {
                    for ($z = $y + 1; $z < $solosCount; $z++) {
                        $candidate = array_merge($duos[$i], [$candidateSolos[$x], $candidateSolos[$y], $candidateSolos[$z]]);
                        if (!$hasRoleConflict($candidate)) {
                            $createSuggestedTeam($candidate);
                            $usedDuoIndices[] = $i;
                            $usedSoloIds[] = $candidateSolos[$x]->id;
                            $usedSoloIds[] = $candidateSolos[$y]->id;
                            $usedSoloIds[] = $candidateSolos[$z]->id;
                            $found = true;
                            break 3;
                        }
                    }
                }
            }
        }

        // 5. Try 5 Solos
        $candidateSolos = [];
        foreach ($solos as $s) {
            if (in_array($s->id, $usedSoloIds)) continue;
            $candidateSolos[] = $s;
        }

        $solosCount = count($candidateSolos);
        for ($a = 0; $a < $solosCount; $a++) {
            for ($b = $a + 1; $b < $solosCount; $b++) {
                for ($c = $b + 1; $c < $solosCount; $c++) {
                    for ($d = $c + 1; $d < $solosCount; $d++) {
                        for ($e = $d + 1; $e < $solosCount; $e++) {
                            $candidate = [$candidateSolos[$a], $candidateSolos[$b], $candidateSolos[$c], $candidateSolos[$d], $candidateSolos[$e]];
                            if (!$hasRoleConflict($candidate)) {
                                $createSuggestedTeam($candidate);
                                $usedSoloIds[] = $candidateSolos[$a]->id;
                                $usedSoloIds[] = $candidateSolos[$b]->id;
                                $usedSoloIds[] = $candidateSolos[$c]->id;
                                $usedSoloIds[] = $candidateSolos[$d]->id;
                                $usedSoloIds[] = $candidateSolos[$e]->id;
                                // Restart search filter to account for used ids
                                $candidateSolos = array_values(array_filter($candidateSolos, function($item) use ($usedSoloIds) {
                                    return !in_array($item->id, $usedSoloIds);
                                }));
                                $solosCount = count($candidateSolos);
                                $a = -1; // resets loop
                                break 4;
                            }
                        }
                    }
                }
            }
        }

        if ($suggestedTeamsCount > 0) {
            AdminActivity::log("Algoritma Matchmaker berhasil mencocokkan & membentuk {$suggestedTeamsCount} tim baru.");
            return back()->with('success', "Berhasil mencocokkan & membentuk {$suggestedTeamsCount} tim solo baru secara otomatis!");
        }

        return back()->with('error', 'Tidak dapat mencocokkan player dengan komposisi role yang ideal saat ini.');
    }

    public function deleteSoloTeam($id)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $team = Team::findOrFail($id);
        
        // Unlink all players assigned to this team (move back to pool)
        \App\Models\SoloPlayer::where('team_id', $team->id)->update([
            'team_id' => null,
            'status' => 'PENDING'
        ]);

        $name = $team->name;
        $team->delete();

        AdminActivity::log('Menghapus tim solo: ' . $name);

        return back()->with('success', 'Tim solo berhasil dihapus dan player dikembalikan ke pool!');
    }

    /**
     * Tampilkan halaman pengelolaan disk/storage media (Superadmin Only)
     */
    public function storageManager()
    {
        if (!Auth::check() || !Auth::user()->hasPermission('manage')) {
            return redirect()->route('admin.login');
        }

        $publicPath = public_path();

        $folders = [
            'chat_uploads' => $publicPath . '/chat_uploads',
            'match_results' => $publicPath . '/match_results',
            'posters' => storage_path('app/public/posters'),
            'certificates' => $publicPath . '/uploads/certificates'
        ];

        $storageData = [];
        $totalSystemSize = 0;

        foreach ($folders as $key => $path) {
            $filesInfo = [];
            $folderSize = 0;
            
            if (file_exists($path) && is_dir($path)) {
                $dirFiles = scandir($path);
                foreach ($dirFiles as $file) {
                    if ($file === '.' || $file === '..') continue;
                    
                    $filePath = $path . '/' . $file;
                    if (is_file($filePath)) {
                        $size = filesize($filePath);
                        $folderSize += $size;
                        
                        $filesInfo[] = [
                            'name' => $file,
                            'size' => $size,
                            'path' => '/' . ($key === 'posters' ? 'storage/posters' : ($key === 'certificates' ? 'uploads/certificates' : $key)) . '/' . $file,
                            'date' => filemtime($filePath)
                        ];
                    }
                }
            }

            // Sort files by date descending
            usort($filesInfo, function($a, $b) {
                return $b['date'] - $a['date'];
            });

            $storageData[$key] = [
                'name' => $key === 'chat_uploads' ? 'Live Chat Aset' : ($key === 'match_results' ? 'Bukti Hasil Laga' : ($key === 'posters' ? 'Poster Season' : 'Template Sertifikat')),
                'path' => $path,
                'total_size' => $folderSize,
                'files_count' => count($filesInfo),
                'files' => $filesInfo
            ];

            $totalSystemSize += $folderSize;
        }

        return view('admin.storage_manager', compact('storageData', 'totalSystemSize'));
    }

    /**
     * Kosongkan folder penyimpanan tertentu (Superadmin Only)
     */
    public function clearStorageFolder(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasPermission('manage')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['folder' => 'required|string']);
        $folderKey = $request->folder;

        $publicPath = public_path();

        $allowedFolders = [
            'chat_uploads' => $publicPath . '/chat_uploads',
            'match_results' => $publicPath . '/match_results',
            'posters' => storage_path('app/public/posters'),
            'certificates' => $publicPath . '/uploads/certificates'
        ];

        if (!array_key_exists($folderKey, $allowedFolders)) {
            return back()->with('error', 'Folder tidak valid.');
        }

        $path = $allowedFolders[$folderKey];
        $deletedCount = 0;

        if (file_exists($path) && is_dir($path)) {
            $dirFiles = scandir($path);
            foreach ($dirFiles as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $filePath = $path . '/' . $file;
                if (is_file($filePath)) {
                    if ($folderKey === 'posters') {
                        // Skip posters that are currently active in database
                        $inUse = Season::where('poster', $file)->exists();
                        if ($inUse) continue;
                    }
                    if ($folderKey === 'certificates') {
                        // Skip templates that are active in database
                        $inUse = CertificateLayout::where('template_path', 'LIKE', '%' . $file)->exists();
                        if ($inUse) continue;
                    }
                    
                    @unlink($filePath);
                    $deletedCount++;
                }
            }
        }

        // Also clean corresponding database records if applicable
        if ($folderKey === 'chat_uploads') {
            \App\Models\SeasonChat::where('message', 'LIKE', '%[IMAGE]:%')->delete();
        } elseif ($folderKey === 'match_results') {
            \App\Models\MatchReport::truncate();
        }

        AdminActivity::log("Membersihkan folder penyimpanan: {$folderKey}. Terhapus {$deletedCount} berkas.");

        return back()->with('success', "Folder berhasil dibersihkan! {$deletedCount} berkas dihapus.");
    }

    /**
     * Hapus berkas tertentu secara spesifik (Superadmin Only)
     */
    public function deleteStorageFile(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasPermission('manage')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file_path' => 'required|string'
        ]);

        $filePathRelative = $request->file_path;
        
        // Prevent directory traversal attacks
        if (strpos($filePathRelative, '..') !== false) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 400);
        }

        $publicPath = public_path();

        if (strpos($filePathRelative, '/storage/posters/') === 0) {
            $absolutePath = storage_path('app/public/posters/' . basename($filePathRelative));
        } else {
            $absolutePath = $publicPath . $filePathRelative;
        }

        if (file_exists($absolutePath) && is_file($absolutePath)) {
            $isAllowedDir = false;
            $allowedDirs = ['/chat_uploads', '/match_results', '/storage/posters', '/uploads/certificates'];
            foreach ($allowedDirs as $dir) {
                if (strpos($filePathRelative, $dir) === 0) {
                    $isAllowedDir = true;
                    break;
                }
            }

            if (!$isAllowedDir) {
                return response()->json(['success' => false, 'message' => 'Direktori tidak diizinkan.'], 403);
            }

            @unlink($absolutePath);

            // Also clean DB entry
            if (strpos($filePathRelative, '/match_results/') === 0) {
                \App\Models\MatchReport::where('image_proof', $filePathRelative)->delete();
            }

            AdminActivity::log("Menghapus berkas penyimpanan: " . basename($filePathRelative));

            return response()->json(['success' => true, 'message' => 'Berkas berhasil dihapus.']);
        }

        return response()->json(['success' => false, 'message' => 'Berkas tidak ditemukan.']);
    }

    /**
     * Tampilkan log sistem Laravel dari storage/logs/laravel.log (Superadmin Only)
     */
    public function laravelLogs()
    {
        if (!Auth::check() || !Auth::user()->hasPermission('manage')) {
            return redirect()->route('admin.login');
        }

        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logPath)) {
            $file = new \SplFileObject($logPath, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();
            
            // Baca 150 baris terakhir secara efisien
            $startLine = max(0, $totalLines - 150);
            $file->seek($startLine);
            
            while (!$file->eof()) {
                $line = trim($file->current());
                if ($line) {
                    $logs[] = $line;
                }
                $file->next();
            }
            
            // Balik urutan agar log terbaru muncul di paling atas
            $logs = array_reverse($logs);
        } else {
            $logs = ["Log kosong. Belum ada aktivitas error log yang dicatat Laravel."];
        }

        return view('admin.laravel_logs', compact('logs'));
    }
}