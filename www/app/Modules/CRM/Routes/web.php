<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CRM\Http\Controllers\CustomerController;

Route::middleware(['web', 'auth'])->prefix('crm')->name('crm.')->group(function () {
    Route::get('/clientes', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/clientes/broadcast', [CustomerController::class, 'broadcast'])->name('customers.broadcast');
});
