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

        Gate::define('is-super-admin', function ($user) {
            return $user->hasRole('Super Admin');
        });

        Gate::define('is-restaurant-manager', function ($user) {
            return $user->hasRole('Restaurant Manager');
        });

        Gate::define('can-revenue-targets', function ($user) {
            return $user->hasAnyRole(['Super Admin', 'Restaurant Manager']);
        });

        Gate::define('is-asst-restaurant-manager', function ($user) {
            return $user->hasRole('Assistant Restaurant Manager');
        });

        Gate::define('is-fb-supervisor', function ($user) {
            return $user->hasRole('F&B Supervisor');
        });

        Gate::define('is-waiter', function ($user) {
            return $user->hasRole('Waiter');
        });

        Gate::define('is-cashier', function ($user) {
            return $user->hasRole('Cashier');
        });

        Gate::define('is-bartender', function ($user) {
            return $user->hasRole('Bartender');
        });

        Gate::define('is-daily-worker', function ($user) {
            return $user->hasRole('Daily Worker');
        });

        Gate::define('is-trainee', function ($user) {
            return $user->hasRole('Trainee');
        });

        Gate::define('access-checklist-app', function ($user) {
            // Cek apakah user punya role apapun (staff, admin, atau super-admin)
            // Kita gunakan hasAnyRole (pastikan array role sesuai database Anda)
            return $user->hasAnyRole(['Super Admin', 'Restaurant Manager', 'Assistant Restaurant Manager', 'F&B Supervisor', 'Waiter', 'Cashier', 'Bartender', 'Daily Worker', 'Trainee']);
        });

        Gate::before(function ($user, $ability) {
            // Jika user punya permission ini di DB Lokal, izinkan.
            if (method_exists($user, 'hasPermissionTo')) {
                return $user->hasPermissionTo($ability) ? true : null;
            }
        });
    }
}
