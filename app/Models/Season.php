<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = [
        'name', 
        'status', 
        'date_info', 
        'wa_link', 
        'price', 
        'slot', 
        'is_open',
        'poster',     
        'prize_pool',
        'rules_link',
        'schedule_info',
        'is_bracket_visible',
        'is_bracket_locked',
        'manual_juara1',
        'manual_juara2',
        'manual_juara3',
        'manual_juara4'
    ];

    protected $casts = [
        'is_bracket_visible' => 'boolean',
        'is_bracket_locked' => 'boolean',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}