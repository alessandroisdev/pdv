<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Inventory\Http\Controllers\ProductController;

Route::middleware(['web', 'auth'])->prefix('estoque')->name('inventory.')->group(function () {
    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');
    Route::get('/produtos/novo', [ProductController::class, 'create'])->name('products.create');
    Route::post('/produtos', [ProductController::class, 'store'])->name('products.store');
});
