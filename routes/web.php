<?php

use App\Http\Middleware\AdminMiddleware;
use App\Livewire\DashboardReport;
use App\Livewire\Login;
use App\Livewire\PosComponent;
use App\Livewire\ProductManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('pos');
    });

    Route::get('/pos', PosComponent::class)->name('pos');

    // Admin-only Routes
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/products', ProductManager::class)->name('products');
        Route::get('/reports', DashboardReport::class)->name('reports');
    });

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
