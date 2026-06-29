<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeasonChat extends Model
{
    protected $fillable = [
        'season_id',
        'sender_session_token',
        'sender_name',
        'message',
        'is_admin',
        'is_read',
        'is_archived'
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
