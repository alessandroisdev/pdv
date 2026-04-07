<?php

use Illuminate\Support\Facades\Route;

Route::get('/module-check', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Core module loaded web routes successfully!',
    ]);
});
