<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Superadmin has absolute power
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Check if user has specific permission
        if (!$user->hasPermission($permission)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman/aksi ini.');
        }

        return $next($request);
    }
}
