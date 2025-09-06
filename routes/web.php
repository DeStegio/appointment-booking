<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Provider\ServiceController;
use App\Http\Controllers\Provider\ProviderScheduleController;
use App\Http\Controllers\Provider\TimeOffController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;

Route::view('/', 'welcome')->name('home');

// Public provider discovery (no closures; cache-friendly)
Route::get('/providers', 'App\\Http\\Controllers\\Public\\ProviderDirectoryController@index')->name('providers.index');
Route::get('/providers/{provider}', 'App\\Http\\Controllers\\Public\\ProviderDirectoryController@show')->name('providers.show');

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
        Route::resource('time-offs', TimeOffController::class)->except(['show']);
    });

// Health check endpoint (no closures -> works with route:cache)
Route::get('/healthz', HealthController::class)->name('healthz');

// Public slots route (no closures)
Route::get('/providers/{provider}/services/{service}/slots', [AppointmentController::class, 'slots'])
    ->name('appointments.slots');

// Create appointment (auth only)
Route::post('/appointments', [AppointmentController::class, 'store'])
    ->middleware('auth')
    ->name('appointments.store');

// Appointment lifecycle routes (auth only, no closures)
Route::middleware('auth')->group(function () {
    Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::patch('/appointments/{appointment}/cancel',   [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});
