<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fiscal\Http\Controllers\TestEngineController;
use App\Modules\Fiscal\Http\Controllers\RecordController;

Route::middleware(['web', 'auth'])->prefix('fiscal')->name('fiscal.')->group(function () {
    Route::get('/records', [RecordController::class, 'index'])->name('records.index');
    Route::post('/records/{document}/cancel', [RecordController::class, 'cancel'])->name('records.cancel');

    Route::get('/sandbox/transmit', [TestEngineController::class, 'sandbox'])->name('sandbox');
    Route::post('/sandbox/ping', [TestEngineController::class, 'ping'])->name('sandbox.ping');
});
