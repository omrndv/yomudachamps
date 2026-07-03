<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime'
    ];

    /**
     * Helper to create a notification easily
     */
    public static function add(string $type, string $title, string $message)
    {
        return self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message
        ]);
    }
}
