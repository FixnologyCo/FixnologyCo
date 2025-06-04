<?php

use Core\Http\Controllers\ConfiguracionesController;
use Core\Http\Controllers\tuPerfilController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/configuraciones', [ConfiguracionesController::class, 'index'])
            ->name('aplicacion.configuraciones');
        Route::get('/editarMiPerfil', [TuPerfilController::class, 'index'])
            ->name('aplicacion.configuraciones.editarMiPerfil');
        Route::put('/editarMiPerfil', [TuPerfilController::class, 'actualizar'])
            ->name('aplicacion.editarMiPerfil.actualizar')
            ->middleware(['auth']);
    });
    Route::post('/perfil/foto', [TuPerfilController::class, 'actualizarFoto'])
        ->name('usuario.actualizar.foto');
});


