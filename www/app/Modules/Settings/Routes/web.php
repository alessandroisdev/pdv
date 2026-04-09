<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Settings\Http\Controllers\SettingController;
use App\Modules\Settings\Http\Controllers\SystemUserController;

Route::middleware(['web', 'auth', 'can:access-settings'])->group(function () {
    Route::get('/configuracoes', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/configuracoes/salvar', [SettingController::class, 'store'])->name('settings.store');
    Route::post('/configuracoes/printer/test', [\App\Modules\Settings\Http\Controllers\PrinterController::class, 'test'])->name('settings.printer.test');

    Route::get('/configuracoes/usuarios', [SystemUserController::class, 'index'])->name('settings.users.index');
    Route::post('/configuracoes/usuarios/datatable', [SystemUserController::class, 'datatable'])->name('settings.users.datatable');
    Route::post('/configuracoes/usuarios', [SystemUserController::class, 'store'])->name('settings.users.store');
    Route::put('/configuracoes/usuarios/{user}', [SystemUserController::class, 'update'])->name('settings.users.update');
    Route::delete('/configuracoes/usuarios/{user}', [SystemUserController::class, 'destroy'])->name('settings.users.destroy');

    // Digital Signage Standby Menu
    Route::get('/configuracoes/standby', [\App\Modules\Settings\Http\Controllers\StandbyMediaController::class, 'index'])->name('settings.standby.index');
    Route::post('/configuracoes/standby/timeout', [\App\Modules\Settings\Http\Controllers\StandbyMediaController::class, 'updateTimeout'])->name('settings.standby.timeout');
    Route::post('/configuracoes/standby', [\App\Modules\Settings\Http\Controllers\StandbyMediaController::class, 'store'])->name('settings.standby.store');
    Route::post('/configuracoes/standby/{media}/move', [\App\Modules\Settings\Http\Controllers\StandbyMediaController::class, 'move'])->name('settings.standby.move');
    Route::delete('/configuracoes/standby/{media}', [\App\Modules\Settings\Http\Controllers\StandbyMediaController::class, 'destroy'])->name('settings.standby.destroy');
});
