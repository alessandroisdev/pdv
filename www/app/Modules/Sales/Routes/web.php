<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sales\Http\Controllers\CashRegisterController;

use App\Modules\Sales\Http\Controllers\PointOfSaleController;

Route::middleware(['web', 'auth'])->prefix('vendas')->name('sales.')->group(function () {
    Route::get('/caixas', [CashRegisterController::class, 'index'])->name('cash_registers.index');
    
    // Frente de Caixa
    Route::get('/pdv', [PointOfSaleController::class, 'index'])->name('pos.board');
    Route::post('/pdv/checkout', [PointOfSaleController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pdv/cupom/{sale}', [PointOfSaleController::class, 'receipt'])->name('pos.receipt');
    Route::post('/pdv/supervisor-override', [PointOfSaleController::class, 'supervisorOverride'])->name('pos.override');

    // Fechamento, Sangrias e Abertura
    Route::post('/pdv/abrir', [PointOfSaleController::class, 'openShift'])->name('pos.open');
    Route::post('/pdv/movimento', [PointOfSaleController::class, 'cashMovement'])->name('pos.movement');
    Route::get('/pdv/fechar', [PointOfSaleController::class, 'closeShiftScreen'])->name('pos.close_screen');
    Route::post('/pdv/fechar', [PointOfSaleController::class, 'closeShift'])->name('pos.close');
});
