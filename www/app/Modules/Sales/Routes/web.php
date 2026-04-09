<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sales\Http\Controllers\CashRegisterController;
use App\Modules\Sales\Http\Controllers\PointOfSaleController;
use App\Modules\Sales\Http\Controllers\CatalogController;

Route::middleware(['web', 'auth'])->prefix('vendas')->name('sales.')->group(function () {
    Route::get('/caixas', [CashRegisterController::class, 'index'])->name('cash_registers.index');
    Route::get('/caixas/exportar', [CashRegisterController::class, 'exportCsv'])->name('cash_registers.export');
    Route::get('/caixas/{id}', [CashRegisterController::class, 'show'])->name('cash_registers.show');
    
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

// Terminal Front de Caixa Físico (Público da rede local, bloqueado via PIN Session)
Route::middleware(['web'])->prefix('terminal')->name('terminal.')->group(function () {
    Route::get('/', [PointOfSaleController::class, 'index'])->name('pos.board');
    Route::post('/checkout', [PointOfSaleController::class, 'checkout'])->name('pos.checkout');
    Route::get('/cupom/{sale}', [PointOfSaleController::class, 'receipt'])->name('pos.receipt');
    
    // CRM In-Terminal
    Route::post('/check-customer', [PointOfSaleController::class, 'checkCustomer'])->name('pos.check_customer');
    Route::post('/register-customer', [PointOfSaleController::class, 'registerCustomer'])->name('pos.register_customer');
    
    Route::post('/abrir', [PointOfSaleController::class, 'openShift'])->name('pos.open');
    Route::post('/movimento', [PointOfSaleController::class, 'cashMovement'])->name('pos.movement');
    Route::get('/fechar', [PointOfSaleController::class, 'closeShiftScreen'])->name('pos.close_screen');
    Route::post('/fechar', [PointOfSaleController::class, 'closeShift'])->name('pos.close');
});

// Catálogo / Cardápio Digital (Acesso Público Mobile / QRCode)
Route::middleware(['web'])->prefix('catalogo')->name('catalog.')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('index');
    Route::post('/checkout', [CatalogController::class, 'checkout'])->name('checkout');
});
