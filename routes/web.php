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
use App\Http\Controllers\Customer\AppointmentController as CustomerAppointmentController;
use App\Http\Controllers\Provider\AppointmentController as ProviderAppointmentController;

Route::view('/', 'welcome')->name('home');

// Public provider directory (allow slug or id via model fallback)
Route::get('/providers', [\App\Http\Controllers\Directory\ProviderDirectoryController::class, 'index'])->name('providers.index');
Route::get('/providers/{provider}', [\App\Http\Controllers\Directory\ProviderDirectoryController::class, 'show'])->name('providers.show');

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

// Public slots view (existing for booking flow and tests)
Route::get('/providers/{provider}/services/{service}/slots', [AppointmentController::class, 'slots'])->name('appointments.slots');

// Public slots JSON for slug-based directory (avoid path conflict by using .json suffix)
Route::get('/providers/{provider:slug}/services/{service:slug}/slots.json', [\App\Http\Controllers\Directory\ProviderDirectoryController::class, 'slots'])
    ->name('providers.service.slots');

// Create appointment (auth only)
Route::post('/appointments', [AppointmentController::class, 'store'])
    ->middleware('auth')
    ->name('appointments.store');

// Appointment lifecycle routes
// Provider-only for confirm/complete
Route::middleware(['auth','role:provider'])->group(function () {
    Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    // Provider Calendar Day View
    Route::get('/provider/calendar', [\App\Http\Controllers\Provider\CalendarController::class, 'day'])
        ->name('calendar.day');
});
// Cancel allowed for any authenticated user (policy will enforce owner rules)
Route::middleware('auth')->group(function () {
    Route::patch('/appointments/{appointment}/cancel',   [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

// Self-service: Customer area
Route::middleware(['auth','role:customer'])
    ->prefix('my')
    ->name('my.')
    ->group(function () {
        Route::get('/appointments', [CustomerAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [CustomerAppointmentController::class, 'show'])->name('appointments.show');
        Route::get('/appointments/{appointment}/reschedule', [CustomerAppointmentController::class, 'edit'])->name('appointments.edit');
        Route::patch('/appointments/{appointment}/reschedule', [CustomerAppointmentController::class, 'update'])->name('appointments.update');
        Route::patch('/appointments/{appointment}/cancel', [CustomerAppointmentController::class, 'cancel'])->name('appointments.cancel');
    });

// Self-service: Provider area
Route::middleware(['auth','role:provider'])
    ->prefix('provider')
    ->name('provider.')
    ->group(function () {
        Route::get('/appointments', [ProviderAppointmentController::class, 'index'])->name('appointments.index');
    });

// Admin area
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'index'])->name('providers.index');
        Route::patch('/providers/{provider}/toggle', [\App\Http\Controllers\Admin\ProviderController::class, 'toggle'])->name('providers.toggle');
    });
