<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Settings\Http\Controllers\SettingController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/configuracoes', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/configuracoes/salvar', [SettingController::class, 'store'])->name('settings.store');
});
