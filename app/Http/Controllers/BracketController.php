<?php

namespace App\Http\Controllers;

use App\Models\Bracket;
use App\Models\Season;
use App\Models\Team;
use App\Models\SeasonChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BracketController extends Controller
{
    /**
     * Reversible secure obfuscation for season IDs to make URLs unguessable
     */
    public static function encodeId($id)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(($id * 98765) . 'y'));
    }

    public static function decodeId($hash)
    {
        $data = str_replace(['-', '_'], ['+', '/'], $hash);
        $decoded = base64_decode($data);
        if (!$decoded || !str_ends_with($decoded, 'y')) return null;
        $val = substr($decoded, 0, -1);
        if (!is_numeric($val)) return null;
        return intval($val) / 98765;
    }

    /**
     * Tampilkan halaman kelola bracket admin
     */
    public function manageBracket($season_id)
    {
        $season = Season::findOrFail($season_id);
        $teams = Team::where('season_id', $season_id)->where('status', 'PAID')->get();
        $brackets = Bracket::where('season_id', $season_id)
            ->with(['team1', 'team2', 'winner'])
            ->orderBy('round_number')
            ->orderBy('match_number')
            ->get();

        // Calculate rounds structure if bracket exists
        $rounds = [];
        if ($brackets->count() > 0) {
            $rounds = $brackets->groupBy('round_number');
        }

        return view('admin.bracket', compact('season', 'teams', 'brackets', 'rounds'));
    }

    /**
     * Generate/Seed bracket baru untuk season
     */
    public function generateBracket(Request $request, $season_id)
    {
        $season = Season::findOrFail($season_id);
        $teams = Team::where('season_id', $season_id)->where('status', 'PAID')->get();

        $teamCount = $teams->count();
        if ($teamCount < 2) {
            return redirect()->back()->withErrors(['error' => 'Minimal harus ada 2 tim yang sudah lunas (PAID) untuk membuat bagan.']);
        }

        // Determine bracket size (power of 2)
        $bracketSize = 2;
        while ($bracketSize < $teamCount) {
            $bracketSize *= 2;
        }

        // Limit range between 4 and 128 (standard)
        if ($bracketSize < 4) {
            $bracketSize = 4;
        }

        $roundsCount = log($bracketSize, 2);

        DB::beginTransaction();
        try {
            // Clear existing brackets for this season
            Bracket::where('season_id', $season_id)->delete();

            // Shuffle teams for fair seeding
            $shuffledTeams = $teams->shuffle()->values();

            // Generate Matches for Round 1
            $matchesInRound1 = $bracketSize / 2;
            $round1Matches = [];

            for ($matchNum = 1; $matchNum <= $matchesInRound1; $matchNum++) {
                $t1Index = ($matchNum - 1) * 2;
                $t2Index = $t1Index + 1;

                $team1 = $shuffledTeams->get($t1Index);
                $team2 = $shuffledTeams->get($t2Index);

                $bracket = new Bracket();
                $bracket->season_id = $season_id;
                $bracket->round_number = 1;
                $bracket->match_number = $matchNum;
                $bracket->team1_id = $team1 ? $team1->id : null;
                $bracket->team2_id = $team2 ? $team2->id : null;
                $bracket->match_time = "20:00 WIB"; // Default time
                $bracket->status = 'upcoming';

                // Handle BYE automatically
                if ($team1 && !$team2 && $t2Index >= $teamCount) {
                    $bracket->winner_id = $team1->id;
                    $bracket->team1_score = 1;
                    $bracket->team2_score = 0;
                    $bracket->status = 'finished';
                }

                $bracket->save();
                $round1Matches[$matchNum] = $bracket;
            }

            // Generate empty matches for subsequent rounds
            for ($round = 2; $round <= $roundsCount; $round++) {
                $matchesInRound = $bracketSize / (pow(2, $round));
                for ($matchNum = 1; $matchNum <= $matchesInRound; $matchNum++) {
                    $bracket = new Bracket();
                    $bracket->season_id = $season_id;
                    $bracket->round_number = $round;
                    $bracket->match_number = $matchNum;
                    $bracket->team1_id = null;
                    $bracket->team2_id = null;
                    $bracket->team1_score = 0;
                    $bracket->team2_score = 0;
                    $bracket->match_time = $this->getDefaultTimeForRound($round);
                    $bracket->status = 'upcoming';
                    $bracket->save();
                }
            }

            // Auto-advance BYEs from Round 1 to Round 2
            foreach ($round1Matches as $matchNum => $match) {
                if ($match->winner_id) {
                    $this->advanceWinner($match);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Bagan tanding berhasil di-generate secara acak untuk ' . $teamCount . ' tim!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal generate bracket: ' . $e->getMessage()]);
        }
    }

    /**
     * Simpan pembaruan hasil tanding/skor/jadwal
     */
    public function updateMatch(Request $request, $season_id)
    {
        $request->validate([
            'match_id' => 'required|exists:brackets,id',
            'team1_score' => 'required|integer|min:0',
            'team2_score' => 'required|integer|min:0',
            'match_time' => 'nullable|string',
            'status' => 'required|in:upcoming,live,finished'
        ]);

        DB::beginTransaction();
        try {
            $match = Bracket::findOrFail($request->match_id);
            $match->team1_score = $request->team1_score;
            $match->team2_score = $request->team2_score;
            $match->match_time = $request->match_time;

            // Auto-finish and auto-advance when scores are unequal (winner exists)
            if ($request->team1_score != $request->team2_score) {
                if (!$match->team1_id && !$match->team2_id) {
                    return response()->json(['success' => false, 'message' => 'Pertandingan kosong tidak dapat diubah skornya.'], 400);
                }
                $match->status = 'finished';
                if ($match->team1_id && $match->team2_id) {
                    $match->winner_id = ($request->team1_score > $request->team2_score) ? $match->team1_id : $match->team2_id;
                } else {
                    $match->winner_id = $match->team1_id ?? $match->team2_id;
                }
            } else {
                // If scores are equal (e.g. reset to 0-0), clear winner and set status to what was requested (must not be finished)
                $match->status = ($request->status === 'finished') ? 'upcoming' : $request->status;
                $match->winner_id = null;
            }

            $match->save();

            // Handle advancing the winner (or clearing it in next round if status reverted)
            $this->advanceWinner($match);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pertandingan berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan halaman landing page season publik (obfuscated slug)
     */
    public function seasonLanding($slug)
    {
        $season_id = is_numeric($slug) ? intval($slug) : self::decodeId($slug);
        if (!$season_id) abort(404);

        $season = Season::findOrFail($season_id);
        
        $brackets = Bracket::where('season_id', $season_id)->get();
        $rounds = $brackets->groupBy('round_number');

        return view('pages.season_landing', compact('season', 'rounds', 'brackets', 'slug'));
    }

    /**
     * Toggle bracket visibility to participants (Admin API)
     */
    public function toggleBracketVisibility($season_id)
    {
        $season = Season::findOrFail($season_id);
        $season->is_bracket_visible = !$season->is_bracket_visible;
        $season->save();

        return response()->json([
            'success' => true,
            'is_bracket_visible' => $season->is_bracket_visible,
            'message' => $season->is_bracket_visible 
                ? 'Bracket sekarang TERLIHAT oleh peserta.' 
                : 'Bracket sekarang TERSEMBUNYI dari peserta.'
        ]);
    }

    /**
     * Rilis halaman publik bagan tanding untuk season tertentu
     */
    public function publicBracket($slug)
    {
        $season_id = is_numeric($slug) ? intval($slug) : self::decodeId($slug);
        if (!$season_id) abort(404);

        $season = Season::findOrFail($season_id);
        $brackets = Bracket::where('season_id', $season_id)
            ->with(['team1', 'team2', 'winner'])
            ->orderBy('round_number')
            ->orderBy('match_number')
            ->get();

        if ($brackets->count() === 0 || !$season->is_bracket_visible) {
            return view('pages.bracket_empty', compact('season'));
        }

        // Group by round to feed frontend template
        $rounds = $brackets->groupBy('round_number');
        $roundsCount = $rounds->count();
        $totalMatches = $brackets->count();
        
        // Count total unique teams
        $teamIds = [];
        foreach($brackets as $b) {
            if ($b->team1_id) $teamIds[] = $b->team1_id;
            if ($b->team2_id) $teamIds[] = $b->team2_id;
        }
        $teamCount = count(array_unique($teamIds));

        return view('pages.bracket', compact('season', 'brackets', 'rounds', 'roundsCount', 'teamCount', 'slug'));
    }

    /**
     * Helper to advance winner to next round or clear them if reverted
     */
    private function advanceWinner(Bracket $match)
    {
        $nextRoundNum = $match->round_number + 1;
        $nextMatchNum = ceil($match->match_number / 2);

        // Find the match in next round
        $nextMatch = Bracket::where('season_id', $match->season_id)
            ->where('round_number', $nextRoundNum)
            ->where('match_number', $nextMatchNum)
            ->first();

        if ($nextMatch) {
            $isTeam1 = ($match->match_number % 2 !== 0);

            if ($match->status === 'finished' && $match->winner_id) {
                if ($isTeam1) {
                    $nextMatch->team1_id = $match->winner_id;
                } else {
                    $nextMatch->team2_id = $match->winner_id;
                }
            } else {
                // If match is reset or status is not finished, clear the team slot in the next round
                if ($isTeam1) {
                    if ($nextMatch->team1_id == $match->winner_id || $nextMatch->team1_id == $match->team1_id || $nextMatch->team1_id == $match->team2_id) {
                        $nextMatch->team1_id = null;
                        $nextMatch->winner_id = null;
                        $nextMatch->status = 'upcoming';
                    }
                } else {
                    if ($nextMatch->team2_id == $match->winner_id || $nextMatch->team2_id == $match->team1_id || $nextMatch->team2_id == $match->team2_id) {
                        $nextMatch->team2_id = null;
                        $nextMatch->winner_id = null;
                        $nextMatch->status = 'upcoming';
                    }
                }
            }
            $nextMatch->save();
            
            // Recursively update if the next match was also finished (clears down the line)
            if ($nextMatch->status === 'finished') {
                $this->advanceWinner($nextMatch);
            }
        }

        // Bronze Match logic: Loser of Semifinals goes to Round (Final) Match 2
        $finalRoundNum = Bracket::where('season_id', $match->season_id)->max('round_number');
        if ($match->round_number === $finalRoundNum - 1) {
            $bronzeMatch = Bracket::where('season_id', $match->season_id)
                ->where('round_number', $finalRoundNum)
                ->where('match_number', 2)
                ->first();

            if ($bronzeMatch) {
                $loserId = null;
                if ($match->status === 'finished' && $match->winner_id) {
                    $loserId = ($match->winner_id == $match->team1_id) ? $match->team2_id : $match->team1_id;
                }

                if ($match->match_number === 1) {
                    $bronzeMatch->team1_id = $loserId;
                } else if ($match->match_number === 2) {
                    $bronzeMatch->team2_id = $loserId;
                }

                if (!$loserId) {
                    $bronzeMatch->winner_id = null;
                    $bronzeMatch->team1_score = 0;
                    $bronzeMatch->team2_score = 0;
                    $bronzeMatch->status = 'upcoming';
                }

                $bronzeMatch->save();
            }
        }
    }

    /**
     * Get default scheduled match times per round index
     */
    private function getDefaultTimeForRound($round)
    {
        $times = [
            1 => "20:00 WIB",
            2 => "20:40 WIB",
            3 => "21:20 WIB",
            4 => "22:00 WIB",
            5 => "22:40 WIB",
            6 => "23:20 WIB",
            7 => "24:00 WIB"
        ];
        return isset($times[$round]) ? $times[$round] : "20:00 WIB";
    }

    /**
     * Update jam main serentak per babak (round)
     */
    public function updateRoundTimes(Request $request, $season_id)
    {
        $request->validate([
            'round_number' => 'required|integer',
            'match_time' => 'required|string'
        ]);

        Bracket::where('season_id', $season_id)
            ->where('round_number', $request->round_number)
            ->update(['match_time' => $request->match_time]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal Babak ' . $request->round_number . ' berhasil diperbarui secara serentak!'
        ]);
    }

    /**
     * Tukar posisi tim di Babak 1 (Drag & Drop)
     */
    public function swapTeams(Request $request, $season_id)
    {
        $request->validate([
            'match1_id' => 'required|exists:brackets,id',
            'slot1' => 'required|in:1,2',
            'match2_id' => 'required|exists:brackets,id',
            'slot2' => 'required|in:1,2'
        ]);

        DB::beginTransaction();
        try {
            $m1 = Bracket::findOrFail($request->match1_id);
            $m2 = Bracket::findOrFail($request->match2_id);

            if ($m1->round_number !== 1 || $m2->round_number !== 1) {
                return response()->json(['success' => false, 'message' => 'Hanya dapat menukar posisi tim di Babak 1.'], 400);
            }

            if ($m1->status === 'finished' || $m2->status === 'finished') {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menukar tim jika pertandingan sudah selesai.'], 400);
            }

            $col1 = 'team' . $request->slot1 . '_id';
            $col2 = 'team' . $request->slot2 . '_id';

            $tempId = $m1->$col1;
            $m1->$col1 = $m2->$col2;
            $m2->$col2 = $tempId;

            // Handle bye checking for Match 1
            if ($m1->team1_id && !$m1->team2_id) {
                $m1->winner_id = $m1->team1_id;
                $m1->team1_score = 1;
                $m1->team2_score = 0;
                $m1->status = 'finished';
            } elseif (!$m1->team1_id && !$m1->team2_id) {
                $m1->winner_id = null;
                $m1->team1_score = 0;
                $m1->team2_score = 0;
                $m1->status = 'upcoming';
            } else {
                $m1->winner_id = null;
                $m1->team1_score = 0;
                $m1->team2_score = 0;
                $m1->status = 'upcoming';
            }
            $m1->save();

            // Handle bye checking for Match 2
            if ($m2->team1_id && !$m2->team2_id) {
                $m2->winner_id = $m2->team1_id;
                $m2->team1_score = 1;
                $m2->team2_score = 0;
                $m2->status = 'finished';
            } elseif (!$m2->team1_id && !$m2->team2_id) {
                $m2->winner_id = null;
                $m2->team1_score = 0;
                $m2->team2_score = 0;
                $m2->status = 'upcoming';
            } else {
                $m2->winner_id = null;
                $m2->team1_score = 0;
                $m2->team2_score = 0;
                $m2->status = 'upcoming';
            }
            $m2->save();

            // Advance winners to Round 2
            $this->advanceWinner($m1);
            $this->advanceWinner($m2);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Posisi tim berhasil ditukar!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menukar posisi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Add Slot YMD untuk di-seed ke bagan
     */
    public function addYmdSlots(Request $request, $season_id)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:50'
        ]);

        $season = Season::findOrFail($season_id);
        
        $existingYmds = Team::where('season_id', $season_id)
            ->where('name', 'LIKE', 'YMD-%')
            ->get();
            
        $maxIndex = 0;
        foreach($existingYmds as $team) {
            $parts = explode('-', $team->name);
            $index = isset($parts[1]) ? intval($parts[1]) : 0;
            if ($index > $maxIndex) {
                $maxIndex = $index;
            }
        }

        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $request->count; $i++) {
                $newIndex = $maxIndex + $i;
                Team::create([
                    'season_id' => $season_id,
                    'trx_id' => 'YMD' . $season_id . '-SLOT-' . strtoupper(\Illuminate\Support\Str::random(4)),
                    'name' => 'YMD-' . $newIndex,
                    'wa_number' => '-',
                    'status' => 'PAID',
                    'is_solo_team' => false,
                    'amount' => 0,
                    'net_amount' => 0
                ]);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menambahkan ' . $request->count . ' slot YMD baru!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan slot: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ganti nama slot YMD menjadi nama tim peserta asli (renaming)
     */
    public function renameYmdSlot(Request $request, $season_id)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'new_name' => 'required|string|max:100',
            'price' => 'required|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            $ymdTeam = Team::where('season_id', $season_id)->findOrFail($request->team_id);
            $oldName = $ymdTeam->name;
            
            // Search for target registered team in the database for this season
            $targetTeam = Team::where('season_id', $season_id)
                ->where('name', trim($request->new_name))
                ->first();

            if (!$targetTeam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tim "' . $request->new_name . '" tidak ditemukan di database season ini. Pastikan nama tim terdaftar.'
                ], 400);
            }

            // Replace occurrences of YMD Team with Target Team in bracket matches
            Bracket::where('season_id', $season_id)
                ->where('team1_id', $ymdTeam->id)
                ->update(['team1_id' => $targetTeam->id]);

            Bracket::where('season_id', $season_id)
                ->where('team2_id', $ymdTeam->id)
                ->update(['team2_id' => $targetTeam->id]);

            Bracket::where('season_id', $season_id)
                ->where('winner_id', $ymdTeam->id)
                ->update(['winner_id' => $targetTeam->id]);

            // Clean up the placeholder team from the database
            $ymdTeam->delete();

            // Create SeasonFinance entry for YMD slot purchase
            if ($request->price > 0) {
                \App\Models\SeasonFinance::create([
                    'season_id' => $season_id,
                    'type' => 'INCOME',
                    'title' => 'Penjualan Slot YMD (' . $oldName . ' ke ' . $targetTeam->name . ')',
                    'amount' => $request->price,
                    'date' => now()->toDateString()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Slot ' . $oldName . ' berhasil dihubungkan dengan tim "' . $targetTeam->name . '" (WA: ' . ($targetTeam->wa_number ?? '-') . ')!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungkan slot: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengembalikan data JSON bagan tanding untuk polling real-time
     */
    public function getBracketData($slug)
    {
        $season_id = is_numeric($slug) ? intval($slug) : self::decodeId($slug);
        if (!$season_id) return response()->json(['success' => false, 'message' => 'Season not found'], 404);

        $matches = Bracket::where('season_id', $season_id)
            ->with(['team1', 'team2'])
            ->get()
            ->map(function($m) {
                return [
                    'id' => $m->id,
                    'round_number' => $m->round_number,
                    'match_number' => $m->match_number,
                    'team1_id' => $m->team1_id,
                    'team1_name' => $m->team1 ? $m->team1->name : null,
                    'team1_wa' => $m->team1 ? $m->team1->wa_number : null,
                    'team2_id' => $m->team2_id,
                    'team2_name' => $m->team2 ? $m->team2->name : null,
                    'team2_wa' => $m->team2 ? $m->team2->wa_number : null,
                    'team1_score' => $m->team1_score,
                    'team2_score' => $m->team2_score,
                    'winner_id' => $m->winner_id,
                    'status' => $m->status,
                    'match_time' => $m->match_time
                ];
            });
        return response()->json(['success' => true, 'matches' => $matches]);
    }

    /**
     * Delete all placeholder YMD slots for this season
     */
    public function deleteAllYmdSlots($season_id)
    {
        try {
            DB::beginTransaction();
            
            $ymdTeams = Team::where('season_id', $season_id)
                ->where('name', 'LIKE', 'YMD-%')
                ->get();
                
            $count = $ymdTeams->count();
            
            foreach ($ymdTeams as $team) {
                Bracket::where('season_id', $season_id)
                    ->where('team1_id', $team->id)
                    ->update(['team1_id' => null, 'winner_id' => null, 'status' => 'upcoming']);
                    
                Bracket::where('season_id', $season_id)
                    ->where('team2_id', $team->id)
                    ->update(['team2_id' => null, 'winner_id' => null, 'status' => 'upcoming']);

                $team->delete();
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus ' . $count . ' slot placeholder YMD!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus slot YMD: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the Bronze Match (Match 2 in final round)
     */
    public function toggleBronzeMatch(Request $request, $season_id)
    {
        $request->validate([
            'active' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();
            $finalRoundNum = Bracket::where('season_id', $season_id)->max('round_number');
            if (!$finalRoundNum) {
                return response()->json(['success' => false, 'message' => 'Bagan belum dibuat.'], 400);
            }

            if ($request->active) {
                $exists = Bracket::where('season_id', $season_id)
                    ->where('round_number', $finalRoundNum)
                    ->where('match_number', 2)
                    ->exists();

                if (!$exists) {
                    $bronze = new Bracket();
                    $bronze->season_id = $season_id;
                    $bronze->round_number = $finalRoundNum;
                    $bronze->match_number = 2;
                    $bronze->status = 'upcoming';
                    $bronze->match_time = '21:20 WIB';
                    
                    $semi1 = Bracket::where('season_id', $season_id)
                        ->where('round_number', $finalRoundNum - 1)
                        ->where('match_number', 1)
                        ->first();
                    $semi2 = Bracket::where('season_id', $season_id)
                        ->where('round_number', $finalRoundNum - 1)
                        ->where('match_number', 2)
                        ->first();

                    if ($semi1 && $semi1->status === 'finished' && $semi1->winner_id) {
                        $bronze->team1_id = ($semi1->winner_id == $semi1->team1_id) ? $semi1->team2_id : $semi1->team1_id;
                    }
                    if ($semi2 && $semi2->status === 'finished' && $semi2->winner_id) {
                        $bronze->team2_id = ($semi2->winner_id == $semi2->team1_id) ? $semi2->team2_id : $semi2->team1_id;
                    }

                    $bronze->save();
                }
                $msg = 'Bracket Bronze Match (Juara 3 & 4) berhasil diaktifkan!';
            } else {
                Bracket::where('season_id', $season_id)
                    ->where('round_number', $finalRoundNum)
                    ->where('match_number', 2)
                    ->delete();
                $msg = 'Bracket Bronze Match dinonaktifkan.';
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $msg
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status Bronze Match: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // Real-Time Live Chat Controller Methods
    // =========================================================================

    /**
     * Get messages for a specific user session in a season (Public API)
     */
    public function getChatMessages(Request $request, $slug)
    {
        $season_id = is_numeric($slug) ? intval($slug) : self::decodeId($slug);
        if (!$season_id) return response()->json(['success' => false, 'message' => 'Invalid Season'], 404);

        $token = $request->query('session_token');
        if (!$token) return response()->json(['success' => false, 'message' => 'Missing session token'], 400);

        $messages = SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Send a message from public user (Public API)
     */
    public function sendChatMessage(Request $request, $slug)
    {
        $season_id = is_numeric($slug) ? intval($slug) : self::decodeId($slug);
        if (!$season_id) return response()->json(['success' => false, 'message' => 'Invalid Season'], 404);

        $token = $request->input('session_token');
        $messageText = $request->input('message');
        if (!$token || !$messageText) {
            return response()->json(['success' => false, 'message' => 'Missing token or message'], 400);
        }

        // Get or assign anonymous name
        $existing = SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->first();

        if ($existing) {
            $senderName = $existing->sender_name;
        } else {
            $uniqueUsersCount = SeasonChat::where('season_id', $season_id)
                ->distinct('sender_session_token')
                ->count('sender_session_token');
            $senderName = 'anonim-' . ($uniqueUsersCount + 1);
        }

        $chat = SeasonChat::create([
            'season_id' => $season_id,
            'sender_session_token' => $token,
            'sender_name' => $senderName,
            'message' => $messageText,
            'is_admin' => false,
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    /**
     * Get all active chat threads for a season (Admin API)
     */
    public function getChatThreads(Request $request, $season_id)
    {
        $isArchived = $request->query('status') === 'archived';

        $threads = SeasonChat::where('season_id', $season_id)
            ->where('is_archived', $isArchived)
            ->select('sender_session_token', 'sender_name', DB::raw('MAX(created_at) as last_chat_time'), DB::raw('SUM(CASE WHEN is_admin = 0 AND is_read = 0 THEN 1 ELSE 0 END) as unread_count'))
            ->groupBy('sender_session_token', 'sender_name')
            ->orderBy('last_chat_time', 'desc')
            ->get();

        foreach ($threads as $t) {
            $lastMsg = SeasonChat::where('season_id', $season_id)
                ->where('sender_session_token', $t->sender_session_token)
                ->orderBy('created_at', 'desc')
                ->first();
            $t->last_message = $lastMsg ? $lastMsg->message : '';
            $t->last_message_is_admin = $lastMsg ? $lastMsg->is_admin : false;
        }

        return response()->json([
            'success' => true,
            'threads' => $threads
        ]);
    }

    /**
     * Get all messages in a specific thread (Admin API)
     */
    public function getThreadMessages($season_id, $token)
    {
        $messages = SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Mark a thread as read (Admin API)
     */
    public function markThreadAsRead($season_id, $token)
    {
        SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->where('is_admin', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Admin reply to a chat thread (Admin API)
     */
    public function replyChatMessage(Request $request, $season_id)
    {
        $token = $request->input('sender_session_token');
        $messageText = $request->input('message');
        if (!$token || !$messageText) {
            return response()->json(['success' => false, 'message' => 'Missing parameter'], 400);
        }

        $threadInfo = SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->first();
        
        $senderName = $threadInfo ? $threadInfo->sender_name : 'anonim';

        $chat = SeasonChat::create([
            'season_id' => $season_id,
            'sender_session_token' => $token,
            'sender_name' => $senderName,
            'message' => $messageText,
            'is_admin' => true,
            'is_read' => true
        ]);

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    /**
     * Upload an image from public user chat (Public API)
     */
    public function uploadChatImage(Request $request, $slug)
    {
        $season_id = is_numeric($slug) ? intval($slug) : self::decodeId($slug);
        if (!$season_id) return response()->json(['success' => false, 'message' => 'Invalid Season'], 404);

        $token = $request->input('session_token');
        if (!$token || !$request->hasFile('image')) {
            return response()->json(['success' => false, 'message' => 'Missing parameter or file'], 400);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,webp,jpg|max:5120'
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        $uploadPath = public_path('chat_uploads');
        
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);
        $imageUrl = '/chat_uploads/' . $filename;

        // Get display name
        $existing = SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->first();

        if ($existing) {
            $senderName = $existing->sender_name;
        } else {
            $uniqueUsersCount = SeasonChat::where('season_id', $season_id)
                ->distinct('sender_session_token')
                ->count('sender_session_token');
            $senderName = 'anonim-' . ($uniqueUsersCount + 1);
        }

        $chat = SeasonChat::create([
            'season_id' => $season_id,
            'sender_session_token' => $token,
            'sender_name' => $senderName,
            'message' => '[IMAGE]:' . $imageUrl,
            'is_admin' => false,
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    /**
     * Delete a chat thread and all its uploaded files (Admin only)
     */
    public function deleteChatThread($season_id, $token)
    {
        $chats = SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->get();

        foreach ($chats as $c) {
            if (str_starts_with($c->message, '[IMAGE]:')) {
                $imgUrl = substr($c->message, 8);
                $filename = basename($imgUrl);
                $publicPath = public_path();
                $filePath = $publicPath . '/chat_uploads/' . $filename;
                
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $c->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Percakapan berhasil dihapus beserta berkas media.'
        ]);
    }

    /**
     * Archive a chat thread to hide it from active list (Admin only)
     */
    public function archiveChatThread($season_id, $token)
    {
        SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Percakapan berhasil diarsipkan.'
        ]);
    }

    /**
     * Clear all chats and delete all uploaded images in the season (Admin only)
     */
    public function clearAllSeasonChats($season_id)
    {
        $chats = SeasonChat::where('season_id', $season_id)->get();
        foreach ($chats as $c) {
            if (str_starts_with($c->message, '[IMAGE]:')) {
                $imgUrl = substr($c->message, 8);
                $filename = basename($imgUrl);
                $publicPath = public_path();
                $filePath = $publicPath . '/chat_uploads/' . $filename;
                
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $c->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Seluruh riwayat chat season ini berhasil dibersihkan.'
        ]);
    }

    /**
     * Admin Upload an image to a chat thread (Admin API)
     */
    public function adminUploadChatImage(Request $request, $season_id, $token)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,webp,jpg|max:5120'
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        $publicPath = public_path();
        $uploadPath = $publicPath . '/chat_uploads';
        
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $filename);
        $imageUrl = '/chat_uploads/' . $filename;

        $chat = SeasonChat::create([
            'season_id' => $season_id,
            'sender_session_token' => $token,
            'sender_name' => 'Admin',
            'message' => '[IMAGE]:' . $imageUrl,
            'is_admin' => true,
            'is_read' => true
        ]);

        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    /**
     * Unarchive a chat thread (Admin only)
     */
    public function unarchiveChatThread($season_id, $token)
    {
        SeasonChat::where('season_id', $season_id)
            ->where('sender_session_token', $token)
            ->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Percakapan berhasil diaktifkan kembali.'
        ]);
    }

    /**
     * Cari pertandingan aktif berdasarkan nomor WA kapten (Public API)
     */
    public function findActiveMatchForReport(Request $request, $slug)
    {
        $season_id = is_numeric($slug) ? intval($slug) : self::decodeId($slug);
        if (!$season_id) {
            return response()->json(['success' => false, 'message' => 'Season tidak valid.']);
        }

        $request->validate([
            'wa_number' => 'required|string'
        ]);

        $waRaw = trim($request->wa_number);
        // Normalize WA
        $waClean = preg_replace('/[^0-9+]/', '', $waRaw);
        if (str_starts_with($waClean, '+62')) {
            $waClean = '0' . substr($waClean, 3);
        } elseif (str_starts_with($waClean, '628')) {
            $waClean = '0' . substr($waClean, 2);
        } elseif (str_starts_with($waClean, '60')) {
            $waClean = '+' . $waClean;
        } elseif (str_starts_with($waClean, '01')) {
            $waClean = '+60' . substr($waClean, 1);
        } elseif (str_starts_with($waClean, '1')) {
            $waClean = '+60' . $waClean;
        } elseif (!str_starts_with($waClean, '0') && !str_starts_with($waClean, '+') && strlen($waClean) > 0) {
            $waClean = '0' . $waClean;
        }

        $team = Team::where('season_id', $season_id)
            ->where('wa_number', $waClean)
            ->first();

        if (!$team) {
            return response()->json(['success' => false, 'message' => 'Nomor WhatsApp tidak terdaftar pada season ini.']);
        }

        // Find active match where winner is TBD (null)
        $match = Bracket::where('season_id', $season_id)
            ->whereNull('winner_id')
            ->whereNotNull('team1_id')
            ->whereNotNull('team2_id')
            ->where(function($q) use ($team) {
                $q->where('team1_id', $team->id)
                  ->orWhere('team2_id', $team->id);
            })
            ->with(['team1', 'team2'])
            ->first();

        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Tidak ditemukan pertandingan aktif yang perlu dilaporkan untuk tim Anda saat ini.']);
        }

        // Check if pending report already exists
        $existingReport = \App\Models\MatchReport::where('bracket_id', $match->id)
            ->where('reporter_team_id', $team->id)
            ->where('status', 'PENDING')
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengirimkan laporan untuk pertandingan ini. Harap tunggu persetujuan admin.'
            ]);
        }

        return response()->json([
            'success' => true,
            'match' => [
                'id' => $match->id,
                'team1_name' => $match->team1->name,
                'team2_name' => $match->team2->name,
                'team1_id' => $match->team1_id,
                'team2_id' => $match->team2_id,
                'round_number' => $match->round_number,
                'match_number' => $match->match_number
            ],
            'reporter_team_id' => $team->id
        ]);
    }

    /**
     * Submit bukti & hasil laga (Public API)
     */
    public function submitMatchReport(Request $request, $slug)
    {
        $season_id = is_numeric($slug) ? intval($slug) : self::decodeId($slug);
        if (!$season_id) {
            return response()->json(['success' => false, 'message' => 'Season tidak valid.']);
        }

        $request->validate([
            'match_id' => 'required|integer',
            'reporter_team_id' => 'required|integer',
            'score_team1' => 'required|integer|min:0|max:5',
            'score_team2' => 'required|integer|min:0|max:5',
            'image' => 'required|image|mimes:jpeg,png,webp,jpg|max:5120'
        ]);

        // Guard: Prevent submitting report for a match that is already verified/finished
        $match = Bracket::find($request->match_id);
        if ($match && $match->status === 'finished') {
            return response()->json([
                'success' => false,
                'message' => 'Pertandingan ini sudah selesai diverifikasi oleh admin. Tidak bisa mengirim laporan lagi.'
            ]);
        }

        // Upload screenshot
        $file = $request->file('image');
        // Force output extension to .jpg due to conversion
        $filename = 'report_' . time() . '_' . uniqid() . '.jpg';
        
        $publicPath = public_path();
        $uploadPath = $publicPath . '/match_results';
        
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $this->compressAndSaveImage($file, $uploadPath, $filename);
        $imageUrl = '/match_results/' . $filename;

        $report = \App\Models\MatchReport::create([
            'bracket_id' => $request->match_id,
            'season_id' => $season_id,
            'reporter_team_id' => $request->reporter_team_id,
            'score_team1' => $request->score_team1,
            'score_team2' => $request->score_team2,
            'image_proof' => $imageUrl,
            'status' => 'PENDING'
        ]);

        $aiApproved = $this->analyzeReportWithAI($report);

        if ($aiApproved) {
            return response()->json([
                'success' => true,
                'message' => 'Hore! Kemenangan Anda berhasil diverifikasi otomatis oleh AI Yomuda. Bagan turnamen telah diperbarui secara instan!'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan skor berhasil dikirim! Menunggu verifikasi manual oleh panitia admin.'
        ]);
    }

    /**
     * Helper to compress uploaded image using GD to save up to 95% disk storage
     */
    private function compressAndSaveImage($uploadedFile, $uploadPath, $filename)
    {
        $sourcePath = $uploadedFile->getRealPath();
        $targetPath = $uploadPath . '/' . $filename;

        try {
            if (!function_exists('imagecreatefromjpeg') && !function_exists('imagecreatefrompng') && !function_exists('imagecreatefromwebp')) {
                $uploadedFile->move($uploadPath, $filename);
                return true;
            }

            list($width, $height, $type) = getimagesize($sourcePath);
            if (!$width || !$height) {
                $uploadedFile->move($uploadPath, $filename);
                return true;
            }

            // Max width 1200px
            $newWidth = $width;
            $newHeight = $height;
            if ($width > 1200) {
                $newWidth = 1200;
                $newHeight = intval(($height / $width) * 1200);
            }

            $thumb = imagecreatetruecolor($newWidth, $newHeight);
            
            // Set white background for converted images
            $white = imagecolorallocate($thumb, 255, 255, 255);
            imagefill($thumb, 0, 0, $white);

            switch($type) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($sourcePath);
                    break;
                case IMAGETYPE_WEBP:
                    $source = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    $uploadedFile->move($uploadPath, $filename);
                    return true;
            }

            if (!$source) {
                $uploadedFile->move($uploadPath, $filename);
                return true;
            }

            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // Save as compressed JPEG
            imagejpeg($thumb, $targetPath, 75);
            
            imagedestroy($thumb);
            imagedestroy($source);
            return true;
        } catch (\Exception $e) {
            try {
                $uploadedFile->move($uploadPath, $filename);
            } catch (\Exception $e2) {
                return false;
            }
            return true;
        }
    }

    /**
     * Tampilkan daftar laporan hasil tanding dari peserta (Admin Only)
     */
    public function adminMatchReports($season_id)
    {
        $season = Season::findOrFail($season_id);
        $reports = \App\Models\MatchReport::where('season_id', $season_id)
            ->with(['bracket', 'reporterTeam', 'bracket.team1', 'bracket.team2'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.match_reports', compact('season', 'reports'));
    }

    /**
     * Setujui Laporan Hasil Laga dan update bagan secara otomatis (Admin Only)
     */
    public function approveMatchReport($id)
    {
        $report = \App\Models\MatchReport::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $match = Bracket::findOrFail($report->bracket_id);
            
            if (!$match->team1_id || !$match->team2_id) {
                return back()->with('error', 'Tim belum siap bertanding.');
            }

            // Guard: Prevent approving if match already has a verified result
            if ($match->status === 'finished') {
                $report->status = 'REJECTED';
                $report->save();
                DB::commit();
                return back()->with('error', 'Pertandingan ini sudah selesai diverifikasi sebelumnya. Laporan ini otomatis ditolak.');
            }

            // Set scores from report
            $match->team1_score = $report->score_team1;
            $match->team2_score = $report->score_team2;
            $match->status = 'finished';

            if ($report->score_team1 != $report->score_team2) {
                $match->winner_id = ($report->score_team1 > $report->score_team2) ? $match->team1_id : $match->team2_id;
            } else {
                return back()->with('error', 'Skor tidak boleh seri untuk menentukan pemenang.');
            }

            $match->save();

            // Advance the winner
            $this->advanceWinner($match);

            // Update report status
            $report->status = 'APPROVED';
            $report->save();

            // Reject all other pending reports for the same match
            \App\Models\MatchReport::where('bracket_id', $match->id)
                ->where('id', '!=', $id)
                ->where('status', 'PENDING')
                ->update(['status' => 'REJECTED']);

            DB::commit();
            return back()->with('success', 'Laporan skor disetujui! Bagan otomatis terupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /**
     * Tolak Laporan Hasil Laga (Admin Only)
     */
    public function rejectMatchReport($id)
    {
        $report = \App\Models\MatchReport::findOrFail($id);
        $report->status = 'REJECTED';
        $report->save();

        return back()->with('success', 'Laporan skor berhasil ditolak.');
    }

    /**
     * Hapus semua laporan tanding & file fisiknya di server untuk mengosongkan storage (Admin Only)
     */
    public function clearAllMatchReports($season_id)
    {
        $reports = \App\Models\MatchReport::where('season_id', $season_id)->get();
        
        $publicPath = public_path();

        foreach ($reports as $report) {
            if ($report->image_proof) {
                $filePath = $publicPath . $report->image_proof;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            $report->delete();
        }

        return back()->with('success', 'Semua laporan hasil tanding beserta berkas screenshot di server berhasil dibersihkan!');
    }

    /**
     * Poll match reports (returns JSON list of reports for real-time sound updates)
     */
    public function pollMatchReports($season_id)
    {
        $reports = Cache::remember("poll_reports_{$season_id}", 5, function () use ($season_id) {
            return \App\Models\MatchReport::where('season_id', $season_id)
                ->select('id', 'status')
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return response()->json([
            'reports' => $reports
        ]);
    }

    private function analyzeReportWithAI($report)
    {
        try {
            $apiKey = \App\Models\Setting::getVal('gemini_api_key', env('GEMINI_API_KEY'));
            if (!$apiKey) {
                $report->ai_status = 'SKIPPED';
                $report->ai_notes = 'Gemini API Key is not set in settings.';
                $report->save();
                return false;
            }

            $match = Bracket::with(['team1', 'team2'])->find($report->bracket_id);
            if (!$match || !$match->team1_id || !$match->team2_id) {
                return false;
            }

            $imagePath = public_path($report->image_proof);
            if (!file_exists($imagePath)) {
                return false;
            }

            $imageData = base64_encode(file_get_contents($imagePath));
            $imageMime = mime_content_type($imagePath);

            $reporterTeam = ($report->reporter_team_id == $match->team1_id) ? $match->team1 : $match->team2;
            $opposingTeam = ($report->reporter_team_id == $match->team1_id) ? $match->team2 : $match->team1;

            $reporterName = $reporterTeam->name;
            $opposingName = $opposingTeam->name;

            $prompt = "You are an automated referee for a Mobile Legends: Bang Bang (MLBB) tournament platform called Yomuda Championship.\n"
                    . "Your job is to analyze the uploaded screenshot of the end-game scoreboard to verify the match result.\n\n"
                    . "Match Details:\n"
                    . "- Team 1: {$match->team1->name}\n"
                    . "- Team 2: {$match->team2->name}\n"
                    . "- Reporter Team: {$reporterName} (This team uploaded the screenshot. In MLBB match results, the person taking the screenshot is ALWAYS on the blue/left side, which means the left side of the scoreboard represents the Reporter Team's performance).\n"
                    . "- Opposing Team: {$opposingName} (Their performance is on the right side of the scoreboard).\n\n"
                    . "Instructions:\n"
                    . "1. Analyze the image to detect the victory/defeat banner or victory indicator.\n"
                    . "2. Check if the left side (Reporter Team) has 'VICTORY' or has a clear victory indicator (higher score, victory title, yellow/gold highlight on user row).\n"
                    . "3. Read the match score (kill score) shown at the top of the scoreboard (e.g. 25 on left, 12 on right).\n"
                    . "4. Determine the winner: Is the winner indeed {$reporterName}?\n"
                    . "5. Verify if the screenshot is valid (is it an MLBB scoreboard screenshot? Is it edited/fake? Is it a duplicate?)\n\n"
                    . "Respond strictly in JSON format with the following fields:\n"
                    . "{\n"
                    . "  \"is_valid\": true/false,\n"
                    . "  \"detected_winner_side\": \"LEFT\" or \"RIGHT\",\n"
                    . "  \"score_left\": 25,\n"
                    . "  \"score_right\": 12,\n"
                    . "  \"confidence_score\": 0.95,\n"
                    . "  \"notes\": \"Explanation of your findings\"\n"
                    . "}";

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $imageMime,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
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
                CURLOPT_TIMEOUT => 25
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);

            if (!empty($error)) {
                throw new \Exception("cURL Error: " . $error);
            }

            $resData = json_decode($response, true);
            
            if (isset($resData['error'])) {
                $errorMsg = $resData['error']['message'] ?? 'Unknown API error';
                throw new \Exception("Gemini API Error: " . $errorMsg);
            }
            
            $jsonText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (empty($jsonText)) {
                throw new \Exception("Empty response from Gemini API.");
            }

            $aiResult = json_decode($jsonText, true);
            if (!$aiResult) {
                throw new \Exception("Failed to parse Gemini response as JSON: " . $jsonText);
            }

            $report->ai_status = ($aiResult['is_valid'] && ($aiResult['confidence_score'] ?? 0) >= 0.90) ? 'SUCCESS' : 'MANUAL_REVIEW';
            $report->ai_notes = "AI Analysis: " . ($aiResult['notes'] ?? 'No notes');
            $report->save();

            if ($report->ai_status === 'SUCCESS' && ($aiResult['detected_winner_side'] ?? '') === 'LEFT') {
                $scoreLeft = intval($aiResult['score_left'] ?? 0);
                $scoreRight = intval($aiResult['score_right'] ?? 0);

                DB::beginTransaction();
                try {
                    $report->score_team1 = ($report->reporter_team_id == $match->team1_id) ? $scoreLeft : $scoreRight;
                    $report->score_team2 = ($report->reporter_team_id == $match->team1_id) ? $scoreRight : $scoreLeft;
                    $report->status = 'APPROVED';
                    $report->save();

                    $match->team1_score = $report->score_team1;
                    $match->team2_score = $report->score_team2;
                    $match->status = 'finished';
                    $match->winner_id = $report->reporter_team_id;
                    $match->save();

                    $this->advanceWinner($match);

                    \App\Models\MatchReport::where('bracket_id', $match->id)
                        ->where('id', '!=', $report->id)
                        ->where('status', 'PENDING')
                        ->update(['status' => 'REJECTED']);

                    DB::commit();
                    return true;
                } catch (\Exception $eEx) {
                    DB::rollBack();
                    throw $eEx;
                }
            }

            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("AI Report Verification failed: " . $e->getMessage());
            $report->ai_status = 'FAILED';
            $report->ai_notes = 'Error: ' . $e->getMessage();
            $report->save();
            return false;
        }
    }
}
