<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $modules = config('system_modules.modules', []);

        foreach ($modules as $module) {
            $modulePath = app_path("Modules/{$module}");

            if (is_dir($modulePath)) {
                // Load Routes
                if (file_exists($routesPath = $modulePath . '/Routes/api.php')) {
                    Route::prefix('api')
                        ->middleware('api')
                        ->group($routesPath);
                }

                if (file_exists($routesPath = $modulePath . '/Routes/web.php')) {
                    Route::middleware('web')
                        ->group($routesPath);
                }

                // Load Views
                if (is_dir($viewsPath = $modulePath . '/Views')) {
                    $this->loadViewsFrom($viewsPath, strtolower($module));
                }

                // Load Migrations
                if (is_dir($migrationsPath = $modulePath . '/Database/Migrations')) {
                    $this->loadMigrationsFrom($migrationsPath);
                }
            }
        }
    }
}
