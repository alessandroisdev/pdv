<?php
use Illuminate\Support\Facades\Route;
use App\Modules\HR\Http\Controllers\EmployeeController;

Route::middleware(['web', 'auth'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees/datatable', [EmployeeController::class, 'datatable'])->name('employees.datatable');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/employees/export', [EmployeeController::class, 'exportCsv'])->name('employees.export');
});
