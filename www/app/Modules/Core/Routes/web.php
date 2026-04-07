<?php

use Illuminate\Support\Facades\Route;

Route::get('/module-check', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Core module loaded web routes successfully!',
    ]);
});
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/ajuda', [\App\Modules\Core\Http\Controllers\HelpController::class, 'index'])->name('core.help.index');
});
