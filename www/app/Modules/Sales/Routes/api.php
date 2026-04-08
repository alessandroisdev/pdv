<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Sales\Http\Controllers\Api\CheckoutController;

Route::middleware('api')->prefix('api/v1/omnichannel')->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'processDeliverySale']);
});
