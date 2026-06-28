<?php

namespace App\Http\Controllers;

use App\Models\Bracket;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BracketController extends Controller
{
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
            $match->status = $request->status;

            if ($request->status === 'finished') {
                if (!$match->team1_id || !$match->team2_id) {
                    return response()->json(['success' => false, 'message' => 'Pertandingan BYE atau tidak lengkap tidak dapat diubah skornya.'], 400);
                }

                if ($request->team1_score === $request->team2_score) {
                    return response()->json(['success' => false, 'message' => 'Skor tanding tidak boleh seri. Harus ada pemenang.'], 400);
                }

                $match->winner_id = ($request->team1_score > $request->team2_score) ? $match->team1_id : $match->team2_id;
            } else {
                // If status reverted back, clear winner
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
     * Rilis halaman publik bagan tanding untuk season tertentu
     */
    public function publicBracket($season_id)
    {
        $season = Season::findOrFail($season_id);
        $brackets = Bracket::where('season_id', $season_id)
            ->with(['team1', 'team2', 'winner'])
            ->orderBy('round_number')
            ->orderBy('match_number')
            ->get();

        if ($brackets->count() === 0) {
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

        return view('pages.bracket', compact('season', 'brackets', 'rounds', 'roundsCount', 'teamCount'));
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
}
