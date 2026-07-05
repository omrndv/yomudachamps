<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'amount',
        'destination_bank',
        'destination_account',
        'recipient_name',
        'status'
    ];
}
