<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Provider\ServiceController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', function () {
    // Placeholder for login attempt handling
})->name('login.attempt');

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'show'])->name('register.show');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store'])->name('register.store');
});

Route::prefix('provider')
    ->name('provider.')
    ->middleware(['auth', 'role:provider'])
    ->group(function () {
        Route::resource('services', ServiceController::class)->except(['show']);
    });

// Health check endpoint
Route::get('/healthz', fn() => 'ok');
