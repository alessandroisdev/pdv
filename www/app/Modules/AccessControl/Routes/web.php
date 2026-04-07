<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AccessControl\Http\Controllers\LoginController;

Route::middleware('web')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
