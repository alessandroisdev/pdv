<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Finance\Http\Controllers\TransactionController;

Route::middleware(['web', 'auth'])->prefix('financeiro')->name('finance.')->group(function () {
    Route::get('/transacoes', [TransactionController::class, 'index'])->name('transactions.index');
});
