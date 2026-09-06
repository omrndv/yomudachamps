<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // 1. Periksa apakah akun sedang dibekukan oleh Superadmin
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->with('error', 'Akun Anda sedang dibekukan sementara oleh Superadmin.');
        }

        // 2. Periksa apakah sesi akun telah diputus secara paksa (Force Logout)
        if (!empty($user->force_logout_at)) {
            $loginTime = $request->session()->get('login_time');
            if ($loginTime && \Carbon\Carbon::parse($loginTime)->lte($user->force_logout_at)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('error', 'Sesi login Anda telah dihentikan oleh Superadmin.');
            }
        }

        // 3. Update last_seen_at (throttle setiap 2 menit agar performa tetap kencang)
        if (Schema::hasColumn('users', 'last_seen_at')) {
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 2) {
                $user->updateQuietly(['last_seen_at' => now()]);
            }
        }

        $response = $next($request);

        // Anti-cache header untuk seluruh response panel admin
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
