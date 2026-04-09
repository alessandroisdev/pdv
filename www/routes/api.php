<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PosApiController;

// Sanctum API Check
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Autenticação Offline do Terminal Desktop
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    // API Privada do ERP para o Desktop 
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/pos/products', [PosApiController::class, 'getProducts']);
        Route::post('/pos/sync-sales', [PosApiController::class, 'syncSales']);
    });
});
