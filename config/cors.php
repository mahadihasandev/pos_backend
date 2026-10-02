<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Optimized for Vercel (Frontend) <-> Render (Backend) cross-origin communication.
    | Handles wildcard Vercel preview deployments (*.vercel.app) as well as
    | custom production domains defined via FRONTEND_URL.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter([
        env('FRONTEND_URL', 'http://localhost:3000'),
    ]),

    'allowed_origins_patterns' => [
        // Allow all Vercel preview & production deployments
        '#^https?://.*\.vercel\.app$#',
        '#^https?://localhost(:\d+)?$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'X-Tenant-Id',
        'X-Request-Id',
    ],

    'max_age' => 86400,

    'supports_credentials' => true,

];
