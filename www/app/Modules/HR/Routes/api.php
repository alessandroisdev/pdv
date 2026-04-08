<?php
use Illuminate\Support\Facades\Route;
use App\Modules\HR\Http\Controllers\EmployeeController;

Route::prefix('api/hr')->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/export', [EmployeeController::class, 'exportCsv']);
});
