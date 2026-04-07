<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Purchasing\Http\Controllers\SupplierController;

Route::middleware(['web', 'auth'])->prefix('compras')->name('purchasing.')->group(function () {
    Route::get('/fornecedores', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/fornecedores/novo', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/fornecedores', [SupplierController::class, 'store'])->name('suppliers.store');
});
