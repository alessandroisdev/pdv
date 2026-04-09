<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CRM\Http\Controllers\CustomerController;
use App\Modules\CRM\Http\Controllers\OpportunityController;

Route::middleware(['web', 'auth'])->prefix('crm')->name('crm.')->group(function () {
    Route::get('/clientes', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/clientes/datatable', [CustomerController::class, 'datatable'])->name('customers.datatable');
    Route::post('/clientes/broadcast', [CustomerController::class, 'broadcast'])->name('customers.broadcast');

    Route::get('/opportunities', [OpportunityController::class, 'board'])->name('opportunities.board');
    Route::post('/opportunities/{opportunity}/stage', [OpportunityController::class, 'updateStage'])->name('opportunities.stage');
});
