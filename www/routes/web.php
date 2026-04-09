<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::post('/dashboard/transactions/datatable', [DashboardController::class, 'transactionsDatatable'])->middleware('auth')->name('dashboard.transactions.datatable');
Route::post('/dashboard/sales/datatable', [DashboardController::class, 'salesDatatable'])->middleware('auth')->name('dashboard.sales.datatable');
Route::redirect('/docs', '/api/documentation');
