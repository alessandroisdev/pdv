<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Purchasing\Http\Controllers\SupplierController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/fornecedores', [SupplierController::class, 'index'])->name('purchasing.suppliers.index');
    Route::post('/fornecedores', [SupplierController::class, 'store'])->name('purchasing.suppliers.store');
    
    // As in the future, we could have PurchaseOrdersController
    // Route::get('/pedidos', ... ) 
});
