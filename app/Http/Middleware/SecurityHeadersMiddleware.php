<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk menyisipkan HTTP Security Headers standar Era 5.0.
 *
 * Melindungi aplikasi dari serangan clickjacking, MIME sniffing,
 * XSS injection, dan membatasi browser permissions.
 */
class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Mencegah Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Mencegah MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Proteksi XSS pada browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Kontrol referer saat navigasi
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Batasi akses sensor / permissions yang tidak dibutuhkan
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        return $response;
    }
}
