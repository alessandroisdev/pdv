<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Fiscal\Http\Controllers\TestEngineController;

Route::middleware(['web', 'auth'])->prefix('fiscal')->name('fiscal.')->group(function () {
    Route::get('/sandbox/transmit', [TestEngineController::class, 'sandbox'])->name('sandbox');
});
