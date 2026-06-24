<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeasonFinance extends Model
{
    protected $fillable = [
        'season_id',
        'type',
        'title',
        'amount',
        'date'
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
