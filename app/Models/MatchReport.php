<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchReport extends Model
{
    protected $fillable = [
        'bracket_id',
        'season_id',
        'reporter_team_id',
        'score_team1',
        'score_team2',
        'image_proof',
        'status'
    ];

    public function bracket(): BelongsTo
    {
        return $this->belongsTo(Bracket::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function reporterTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'reporter_team_id');
    }
}
