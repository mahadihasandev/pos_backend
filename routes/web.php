<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\OrderAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Stateful Admin Dashboard)
|--------------------------------------------------------------------------
| Session-authenticated, CSRF-protected Blade view responses.
*/

Route::get('/', function () {
    return redirect('/admin/orders');
});

Route::prefix('admin')->group(function (): void {
    Route::get('/orders', [OrderAdminController::class, 'index'])->name('admin.orders.index');
});
