<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrisTransaction extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'trx_id',
        'base_amount',
        'unique_code',
        'amount',
        'qris_string',
        'status',
        'expires_at',
        'paid_at',
        'gopay_reference'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Relasi ke tim pendaftar
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'trx_id', 'trx_id');
    }
}
