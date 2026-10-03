<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all upstream reverse proxies & load balancers (FrankenPHP, Render, Cloudflare, AWS)
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB
        );

        // Security headers (HSTS, nosniff, etc.) and TLS enforcement after proxy headers are resolved
        $middleware->append(\App\Http\Middleware\EnforceTlsAndSecurityHeaders::class);

        // Redirect unauthenticated guests to Admin Login
        $middleware->redirectGuestsTo('/admin/login');

        // Stateful Web / Admin middleware group
        $middleware->web(append: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);

        // Stateless API middleware group
        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ], append: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
        ]);

        // Route Aliases
        $middleware->alias([
            'auth.api' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Enforce strict JSON responses on stateless API requests without redirecting
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e): bool {
            return $request->is('api/*') || $request->expectsJson();
        });

        // Global uniform API error envelope
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => $statusCode,
                        'message' => $e->getMessage() ?: 'An unexpected server error occurred.',
                        'trace_id' => $request->header('X-Request-Id', (string) str()->uuid()),
                    ],
                ], $statusCode);
            }
        });
    })
    ->create();
