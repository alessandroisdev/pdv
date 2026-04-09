<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::post('/dashboard/transactions/datatable', [DashboardController::class, 'transactionsDatatable'])->middleware('auth')->name('dashboard.transactions.datatable');
Route::post('/dashboard/sales/datatable', [DashboardController::class, 'salesDatatable'])->middleware('auth')->name('dashboard.sales.datatable');
Route::redirect('/docs', '/api/documentation');

// B2B Customer Portal Routes (Public/Self-Service)
use App\Http\Controllers\CustomerPortalController;
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [CustomerPortalController::class, 'showLoginForm'])->name('login');
    Route::post('/authenticate', [CustomerPortalController::class, 'authenticate'])->name('authenticate');
    Route::get('/dashboard', [CustomerPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/installments/{id}/pix', [CustomerPortalController::class, 'generatePix'])->name('installments.pix');
    Route::post('/logout', [CustomerPortalController::class, 'logout'])->name('logout');
});

// PWA Omnichannel Catalog Routes (Public)
use App\Http\Controllers\CatalogController;
// KDS Kitchen Display System (Realtime)
Route::middleware(['auth'])->group(function () {
    Route::get('/kds', [\App\Http\Controllers\KdsController::class, 'index'])->name('kds.index');
});
Route::prefix('catalogo')->name('catalog.')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('index');
    Route::post('/checkout', [CatalogController::class, 'checkout'])->name('checkout');
});
