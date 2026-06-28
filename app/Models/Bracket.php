<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bracket extends Model
{
    protected $fillable = [
        'season_id',
        'round_number',
        'match_number',
        'team1_id',
        'team2_id',
        'team1_score',
        'team2_score',
        'winner_id',
        'match_time',
        'status'
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function winner()
    {
        return $this->belongsTo(Team::class, 'winner_id');
    }
}
