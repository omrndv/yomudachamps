<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Tambahkan baris ini Nadiv:
    protected $fillable = ['key', 'value'];

    /**
     * Helper untuk mengambil nilai setting berdasarkan key
     */
    public static function getVal($key)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : null;
    }
}