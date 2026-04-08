<?php

namespace App\Modules\CRM\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CRMServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Views', 'crm');

        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');
            
        // Se houver API para Webhook futuramente
        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::middleware('api')
                ->prefix('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }
    }
}
