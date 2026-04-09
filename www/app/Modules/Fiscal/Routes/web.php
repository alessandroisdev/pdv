<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fiscal\Http\Controllers\TestEngineController;
use App\Modules\Fiscal\Http\Controllers\RecordController;

Route::middleware(['web', 'auth'])->prefix('fiscal')->name('fiscal.')->group(function () {
    Route::get('/records', [RecordController::class, 'index'])->name('records.index');
    Route::post('/records/datatable', [RecordController::class, 'datatable'])->name('records.datatable');
    Route::post('/records/{document}/cancel', [RecordController::class, 'cancel'])->name('records.cancel');

    Route::get('/sandbox/transmit', [TestEngineController::class, 'sandbox'])->name('sandbox');
    Route::post('/sandbox/ping', [TestEngineController::class, 'ping'])->name('sandbox.ping');

    // Módulo de Configuração (Tax Engine)
    Route::prefix('configuracoes')->name('settings.')->group(function () {
        Route::get('/tributos', [\App\Modules\Fiscal\Http\Controllers\TaxRuleController::class, 'index'])->name('taxes');
        Route::post('/tributos/datatable', [\App\Modules\Fiscal\Http\Controllers\TaxRuleController::class, 'datatable'])->name('taxes.datatable');
        Route::post('/tributos', [\App\Modules\Fiscal\Http\Controllers\TaxRuleController::class, 'store'])->name('taxes.store');
    });
});
