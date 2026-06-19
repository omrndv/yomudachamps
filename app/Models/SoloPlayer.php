<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoloPlayer extends Model
{
    protected $fillable = [
        'season_id',
        'team_id',
        'wa_number',
        'role',
        'rank',
        'status',
        'amount_paid',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
