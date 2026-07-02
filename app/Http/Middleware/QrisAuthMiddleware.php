<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class QrisAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Gunakan otentikasi standard Laravel Admin
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Cek perizinan akses "settings" (Super Admin yang bisa kelola sistem)
        if (!Auth::user()->hasPermission('settings')) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka Dips Gateway.');
        }

        return $next($request);
    }
}
