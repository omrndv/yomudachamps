<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BracketSnapshot extends Model
{
    protected $fillable = [
        'season_id',
        'step_number',
        'is_current',
        'action_name',
        'bracket_data',
        'team_data',
        'season_meta',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'bracket_data' => 'array',
        'team_data' => 'array',
        'season_meta' => 'array',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
