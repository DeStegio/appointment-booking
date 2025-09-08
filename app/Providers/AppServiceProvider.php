<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom binding: accept provider by slug or id, and ensure role is provider
        Route::bind('provider', function ($value) {
            return User::query()
                ->where('role', 'provider')
                ->where(function ($q) use ($value) {
                    $q->where('slug', $value)->orWhere('id', $value);
                })
                ->firstOrFail();
        });
    }
}
