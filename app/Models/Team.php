<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'season_id', 
        'trx_id', 
        'name', 
        'wa_number', 
        'status', 
        'tripay_reference', // Referensi unik dari Tripay
        'status_tripay',    // Status asli dari Tripay (PAID, UNPAID, EXPIRED)
        'payment_method'    // Metode yang dipilih (QRIS, BRIVA, dll)
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}