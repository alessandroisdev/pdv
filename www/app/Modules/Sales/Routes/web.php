<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sales\Http\Controllers\CashRegisterController;

use App\Modules\Sales\Http\Controllers\PointOfSaleController;

Route::middleware(['web', 'auth'])->prefix('vendas')->name('sales.')->group(function () {
    Route::get('/caixas', [CashRegisterController::class, 'index'])->name('cash_registers.index');
    
    // Frente de Caixa
    Route::get('/pdv', [PointOfSaleController::class, 'index'])->name('pos.board');
    Route::post('/pdv/checkout', [PointOfSaleController::class, 'checkout'])->name('pos.checkout');
});
