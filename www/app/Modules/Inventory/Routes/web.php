<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Inventory\Http\Controllers\ProductController;
use App\Modules\Inventory\Http\Controllers\CategoryController;
use App\Modules\Inventory\Http\Controllers\LabelController;

Route::middleware(['web', 'auth'])->prefix('estoque')->name('inventory.')->group(function () {
    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');
    Route::get('/produtos/novo', [ProductController::class, 'create'])->name('products.create');
    Route::post('/produtos', [ProductController::class, 'store'])->name('products.store');
    
    Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
    
    // Motor de Etiquetas (Labels)
    Route::get('/etiquetas', [LabelController::class, 'index'])->name('labels.index');
    Route::post('/etiquetas/gerar', [LabelController::class, 'generate'])->name('labels.generate');
});
