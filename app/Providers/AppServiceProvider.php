<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

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
        //
        Paginator::useBootstrapFive();

        Gate::define('access-checklist-app', function ($user) {
            return $user->hasAnyRole(['Super Admin', 'Restaurant Manager', 'Assistant Restaurant Manager', 'F&B Supervisor', 'Waiter', 'Cashier', 'Bartender', 'Daily Worker', 'Trainee']);
        });
    }
}
