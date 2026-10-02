<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\CustomerController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\ProductController;
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

// Authentication Endpoints
Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Catalog & Inventory Endpoints
Route::prefix('products')->group(function (): void {
    Route::get('/', [ProductController::class, 'index']);
});

// Customer CRM Endpoints
Route::prefix('customers')->group(function (): void {
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/', [CustomerController::class, 'store']);
});

// Orders & POS Checkout Endpoints
Route::prefix('orders')->group(function (): void {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/metrics', [OrderController::class, 'metrics']);
    Route::get('/{uuid}', [OrderController::class, 'show']);
});
