<?php
use Illuminate\Support\Facades\Route;
use App\Modules\Finance\Http\Controllers\TransactionController;

Route::prefix('api/finance')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/export', [TransactionController::class, 'exportCsv']);
});
