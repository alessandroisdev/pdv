<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AccessControl\Http\Controllers\LoginController;
// use App\Modules\AccessControl\Http\Controllers\UserController;

Route::middleware('web')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    // Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');

    // Gestão de Colaboradores de Caixa
    Route::get('/colaboradores', [\App\Modules\AccessControl\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/colaboradores', [\App\Modules\AccessControl\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/colaboradores/{employee}', [\App\Modules\AccessControl\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/colaboradores/{employee}', [\App\Modules\AccessControl\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/colaboradores/{employee}/toggle-status', [\App\Modules\AccessControl\Http\Controllers\EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::get('/colaboradores/{employee}/badge', [\App\Modules\AccessControl\Http\Controllers\EmployeeController::class, 'badge'])->name('employees.badge');

    // Módulo de Auditoria Global de Segurança
    Route::get('/auditoria', [\App\Modules\AccessControl\Http\Controllers\AuditController::class, 'index'])->name('audit.index');
});
