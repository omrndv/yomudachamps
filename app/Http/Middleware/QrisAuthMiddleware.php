<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class QrisAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('qris_authenticated') || session('qris_authenticated') !== true) {
            return redirect()->route('qris.login');
        }

        return $next($request);
    }
}
