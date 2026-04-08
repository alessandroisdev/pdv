<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Settings\Http\Controllers\Api\SignageApiController;

Route::middleware('api')->prefix('v1')->group(function () {
    Route::get('/signage', [SignageApiController::class, 'index']);
});
