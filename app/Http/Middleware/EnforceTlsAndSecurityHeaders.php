<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceTlsAndSecurityHeaders
{
    /**
     * Handle an incoming request and enforce TLS/Security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shouldForceHttps = (bool) env('FORCE_HTTPS', false) || app()->isProduction();

        // Check if request is secure directly, via Symfony trusted proxy, or reverse proxy forwarded headers
        $isSecure = $request->isSecure()
            || strtolower((string) $request->header('x-forwarded-proto')) === 'https'
            || strtolower((string) $request->server('HTTP_X_FORWARDED_PROTO')) === 'https';

        // Redirect plain HTTP requests to HTTPS when TLS is enforced
        if ($shouldForceHttps && ! $isSecure) {
            return redirect()->secure($request->getRequestUri(), 308);
        }

        /** @var Response $response */
        $response = $next($request);

        // Attach Strict-Transport-Security (HSTS) when running on HTTPS
        if ($isSecure) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
