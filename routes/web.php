<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Provider\ServiceController;
use App\Http\Controllers\Provider\ProviderScheduleController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\DashboardController;

Route::view('/', 'welcome')->name('home');

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Authenticated routes
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

// Provider area
Route::prefix('provider')
    ->name('provider.')
    ->middleware(['auth', 'role:provider'])
    ->group(function () {
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('schedules', ProviderScheduleController::class)->except(['show']);
    });

// Health check endpoint (no closures → works with route:cache)
Route::get('/healthz', HealthController::class)->name('healthz');
