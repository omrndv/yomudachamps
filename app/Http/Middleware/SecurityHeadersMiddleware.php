<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply security headers to the response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Enforce Strict-Transport-Security (HSTS) - 1 Year
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent Clickjacking (allow same origin)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Enable Browser XSS filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy (allows required CDNs and resources)
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; " .
               "font-src 'self' data: https://cdn.jsdelivr.net https://fonts.gstatic.com; " .
               "img-src 'self' data: blob: https:; " .
               "connect-src 'self' https:; " .
               "frame-ancestors 'self';";
        $response->headers->set('Content-Security-Policy', $csp);

        // Anti-Cache for Admin & Auth Routes (Prevents proxy/browser credential caching)
        if ($request->is('admin*') || $request->is('login*') || $request->is('logout*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
