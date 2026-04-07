<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AccessControl\Http\Controllers\LoginController;
use App\Modules\AccessControl\Http\Controllers\UserController;

Route::middleware('web')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');

    // Gestão de Colaboradores de Caixa
    Route::get('/colaboradores', [\App\Modules\AccessControl\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/colaboradores', [\App\Modules\AccessControl\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');

    // Módulo de Auditoria Global de Segurança
    Route::get('/auditoria', [\App\Modules\AccessControl\Http\Controllers\AuditController::class, 'index'])->name('audit.index');
});
