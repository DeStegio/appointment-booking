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

// Registration routes
Route::view('/register', 'auth.register')->name('register.show');
Route::post('/register', function () {
    // Placeholder for register handling
})->name('register.store');

Route::prefix('provider')
    ->name('provider.')
    ->middleware(['auth', 'role:provider'])
    ->group(function () {
        Route::resource('services', ServiceController::class)->except(['show']);
    });

// Health check endpoint
Route::get('/healthz', fn() => 'ok');

// Diagnostic plain login page (no session/views)
Route::get('/login-plain', fn() => response('<h1>login-plain</h1>', 200));
