<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Prefix: api/v1)
|--------------------------------------------------------------------------
| Strictly stateless routes authenticated via Bearer tokens.
| Rate-limited and returning uniform JSON responses.
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'runtime' => 'Laravel Octane / FrankenPHP',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Domain API Endpoints
Route::prefix('orders')->group(function (): void {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/metrics', [OrderController::class, 'metrics']);
    Route::get('/{uuid}', [OrderController::class, 'show']);
});
