<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Purchasing\Http\Controllers\SupplierController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/fornecedores', [SupplierController::class, 'index'])->name('purchasing.suppliers.index');
    Route::post('/fornecedores', [SupplierController::class, 'store'])->name('purchasing.suppliers.store');
    
    Route::get('/compras/pedidos', [\App\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'index'])->name('purchasing.orders.index');
    Route::get('/compras/pedidos/novo', [\App\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'create'])->name('purchasing.orders.create');
    Route::post('/compras/pedidos/salvar', [\App\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'store'])->name('purchasing.orders.store');
    Route::post('/compras/pedidos/{order}/receber', [\App\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'receive'])->name('purchasing.orders.receive');
    
    // Internal API for product search
    Route::get('/compras/api/produtos', [\App\Modules\Purchasing\Http\Controllers\PurchaseOrderController::class, 'searchProduct'])->name('purchasing.api.products');
});
