<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AdminActivity extends Model
{
    protected $fillable = [
        'user_id',
        'activity',
        'ip_address',
        'user_agent',
        'device',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($activity)
    {
        $userAgent = request()->userAgent();
        $ip = request()->header('X-Forwarded-For') ?? request()->ip();
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }
        
        $device = self::parseUserAgent($userAgent);

        return self::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'device' => $device,
        ]);
    }

    private static function parseUserAgent($ua)
    {
        if (empty($ua)) {
            return 'Unknown Device';
        }

        // Detect OS
        $os = 'Unknown OS';
        if (preg_match('/macintosh|mac os x/i', $ua)) {
            $os = 'Mac';
        } elseif (preg_match('/windows|win32/i', $ua)) {
            $os = 'Windows';
        } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
            $os = 'iOS';
        } elseif (preg_match('/android/i', $ua)) {
            $os = 'Android';
        } elseif (preg_match('/linux/i', $ua)) {
            $os = 'Linux';
        }

        // Detect Browser
        $browser = 'Unknown Browser';
        if (preg_match('/chrome/i', $ua) && !preg_match('/edge|edg/i', $ua) && !preg_match('/opr/i', $ua)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) {
            $browser = 'Safari';
        } elseif (preg_match('/firefox/i', $ua)) {
            $browser = 'Firefox';
        } elseif (preg_match('/edge|edg/i', $ua)) {
            $browser = 'Edge';
        } elseif (preg_match('/opr/i', $ua) || preg_match('/opera/i', $ua)) {
            $browser = 'Opera';
        }

        // Detect Device Type
        $deviceType = 'Desktop';
        if (preg_match('/mobile|phone|ipod/i', $ua)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/tablet|ipad|playbook|silk/i', $ua)) {
            $deviceType = 'Tablet';
        }

        return "{$os} ({$browser} - {$deviceType})";
    }
}
