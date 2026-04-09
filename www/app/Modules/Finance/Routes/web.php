<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Finance\Http\Controllers\TransactionController;
use App\Modules\Finance\Http\Controllers\DashboardController;

Route::middleware(['web', 'auth', 'can:access-finance'])->prefix('financeiro')->name('finance.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transacoes', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transacoes/datatable', [TransactionController::class, 'datatable'])->name('transactions.datatable');
    Route::get('/transacoes/export', [TransactionController::class, 'exportCsv'])->name('transactions.export');
    Route::get('/relatorios', [\App\Modules\Finance\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/dre', [\App\Modules\Finance\Http\Controllers\DreController::class, 'index'])->name('dre.index');
    
    // Tesouraria: Contas a Pagar e Receber
    Route::get('/tesouraria', [\App\Modules\Finance\Http\Controllers\InstallmentController::class, 'index'])->name('installments.index');
    Route::post('/tesouraria/datatable', [\App\Modules\Finance\Http\Controllers\InstallmentController::class, 'datatable'])->name('installments.datatable');
    Route::post('/tesouraria', [\App\Modules\Finance\Http\Controllers\InstallmentController::class, 'store'])->name('installments.store');
    Route::post('/tesouraria/{installment}/pay', [\App\Modules\Finance\Http\Controllers\InstallmentController::class, 'pay'])->name('installments.pay');
});
