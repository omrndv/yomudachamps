<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateLayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'template_path',
        'font_path',
        'font_size',
        'font_color',
        'pos_x',
        'pos_y',
        'layout_data',
        'google_drive_link',
    ];

    protected $casts = [
        'layout_data' => 'array',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
