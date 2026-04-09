<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once __DIR__ . '/../helpers.php';
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Intercepta todas as validações de Gate; se for 'Super Admin', libera geral
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasRole('Super Admin')) {
                return true;
            }
        });

        // Para outras checagens de "can('ver-financeiro')", Laravel chamará os Gates.
        // Já que nossa trait em User tem um método hasPermissionTo, 
        // mas não definimos Gate nativo um por um, vamos injetar uma macro de interceptação ou apenas usar no Middleware.
        \Illuminate\Support\Facades\Gate::define('access-finance', function ($user) {
            return $user->hasRole('Admin') || $user->hasRole('Gestor Financeiro') || $user->hasPermissionTo('access-finance');
        });

        \Illuminate\Support\Facades\Gate::define('access-settings', function ($user) {
            return $user->hasRole('Super Admin'); // Apenas donos
        });
    }
}
