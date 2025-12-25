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
            return redirect()->route('admin.seasons');
        }
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $admin_user = 'admin';
        $admin_pass = 'yomuda123';

        if ($request->username == $admin_user && $request->password == $admin_pass) {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.seasons');
        }
        return back()->with('error', 'Username atau Password salah!');
    }

    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }

    public function seasons()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $seasons = \App\Models\Season::withCount('teams')
            ->orderBy('status', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.seasons', compact('seasons'));
    }

    public function dashboard($season_id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $current_season = Season::findOrFail($season_id);
        
        $filtered_teams = Team::where('season_id', $season_id)->get();

        $paid_teams = Team::where('season_id', $season_id)
            ->where('status', 'PAID')
            ->get();

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
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return view('admin.settings');
    }

    public function bulkStore(Request $request, $season_id)
    {
        $rawData = $request->input('bulk_data');
        $lines = explode("\n", str_replace("\r", "", $rawData));

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $data = preg_split('/\t+/', $line);
            if (count($data) < 2) {
                $data = preg_split('/\s+(?=\d+$)/', trim($line));
            }

            if (count($data) >= 2) {
                // Simpan ke database
                Team::create([
                    'season_id' => $season_id,
                    'trx_id'    => 'YMD' . $season_id . '-' . strtoupper(Str::random(4)),
                    'name'      => trim($data[0]),
                    'wa_number' => trim($data[1]),
                    'status'    => 'PAID'
                ]);
            }
        }

        return back()->with('success', 'Data berhasil diimport ke Database!');
    }

    public function deleteTeam($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $team = \App\Models\Team::findOrFail($id);
        $team->delete();

        return back()->with('success', 'Tim berhasil dihapus dari daftar!');
    }

    public function deleteAllTeams($season_id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        \App\Models\Team::where('season_id', $season_id)->delete();

        return back()->with('success', 'Semua data tim di season ini berhasil dibersihkan!');
    }

    public function storeSeason(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        \App\Models\Season::create([
            'name'      => $request->name,
            'status'    => 'ACTIVE',
            'date_info' => $request->date_info,
            'wa_link'   => $request->wa_link,
            'price'     => $request->price, 
            'slot'      => $request->slot,  
            'is_open'   => $request->is_open ?? 1, 
        ]);

        return back()->with('success', 'Season baru berhasil dibuat!');
    }

    public function updateSeason(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $season = \App\Models\Season::findOrFail($id);
        $season->update([
            'name'      => $request->name,
            'status'    => $request->status,
            'date_info' => $request->date_info,
            'wa_link'   => $request->wa_link,
            'price'     => $request->price, 
            'slot'      => $request->slot,  
            'is_open'   => $request->is_open,
        ]);

        return back()->with('success', 'Season berhasil diperbarui!');
    }

    public function deleteSeason($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $season = \App\Models\Season::findOrFail($id);

        $season->delete();

        return back()->with('success', 'Season berhasil dihapus!');
    }

    public function updateTeam(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $team = \App\Models\Team::findOrFail($id);

        $team->update([
            'name'      => strtoupper($request->name),
            'wa_number' => $request->wa_number,
            'status'    => $request->status,
        ]);

        return back()->with('success', 'Data tim ' . $team->name . ' berhasil diperbarui!');
    }
}
