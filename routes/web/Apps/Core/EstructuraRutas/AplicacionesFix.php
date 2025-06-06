<?php

use Core\Http\Controllers\AplicacionWebController;
use Core\Http\Controllers\tuPerfilController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/misAplicaciones', [AplicacionWebController::class, 'show'])
            ->name('aplicacion.misAplicaciones');
        });
        
        Route::post('{aplicacion}/{rol}/createApp', [AplicacionWebController::class, 'createApp'])->name('registro.app');
});