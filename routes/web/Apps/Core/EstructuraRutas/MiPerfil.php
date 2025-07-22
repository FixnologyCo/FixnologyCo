<?php

use Core\Http\Controllers\MiPerfilController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/miPerfil', [MiPerfilController::class, 'index'])
            ->name('aplicacion.miPerfil');
        Route::get('/editarMiPerfil', [MiPerfilController::class, 'formUpdate'])
            ->name('aplicacion.miPerfil.editarMiPerfil');
        Route::put('/editarMiPerfil', [MiPerfilController::class, 'actualizar'])
            ->name('aplicacion.editarMiPerfil.actualizar')
            ->middleware(['auth']);
    });
});


