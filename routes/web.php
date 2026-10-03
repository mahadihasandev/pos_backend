<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AccountingAdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CustomerAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Stateful Admin Dashboard)
|--------------------------------------------------------------------------
| Session-authenticated, CSRF-protected Blade view responses.
*/

// Root & Fallback Auth Redirects
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.orders.index')
        : redirect()->route('admin.login');
});

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Admin Authentication
Route::prefix('admin')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');

    // Protected Admin Routes
    Route::middleware('auth')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
        
        // Orders & Sales Feed
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('admin.orders.index');
        
        // Staff & Designation Management
        Route::get('/users', [UserAdminController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserAdminController::class, 'store'])->name('admin.users.store');
        Route::post('/users/{id}/toggle', [UserAdminController::class, 'toggleStatus'])->name('admin.users.toggle');

        // Customer CRM & Khata Due Ledger
        Route::get('/customers', [CustomerAdminController::class, 'index'])->name('admin.customers.index');
        Route::post('/customers', [CustomerAdminController::class, 'store'])->name('admin.customers.store');

        // Financial Accounting & P&L
        Route::get('/accounting', [AccountingAdminController::class, 'index'])->name('admin.accounting.index');
    });
});
