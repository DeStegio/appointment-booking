<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Provider\ServiceController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Auth\LoginAttemptController;
use App\Http\Controllers\Auth\RegisterStoreController;
use App\Http\Controllers\Auth\LoginPlainController;

Route::view('/', 'welcome')->name('home');

// Authentication routes
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', LoginAttemptController::class)->name('login.attempt');

// Registration routes
Route::view('/register', 'auth.register')->name('register.show');
Route::post('/register', RegisterStoreController::class)->name('register.store');

Route::prefix('provider')
    ->name('provider.')
    ->middleware(['auth', 'role:provider'])
    ->group(function () {
        Route::resource('services', ServiceController::class)->except(['show']);
    });

// Health check endpoint
Route::get('/healthz', HealthController::class)->name('healthz');

// Diagnostic plain login page (no session/views)
Route::get('/login-plain', LoginPlainController::class);
