<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Finance\Http\Controllers\TransactionController;
use App\Modules\Finance\Http\Controllers\DashboardController;

Route::middleware(['web', 'auth'])->prefix('financeiro')->name('finance.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transacoes', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transacoes/export', [TransactionController::class, 'exportCsv'])->name('transactions.export');
});
