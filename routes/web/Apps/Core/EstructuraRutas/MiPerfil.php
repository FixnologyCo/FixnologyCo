<?php

use Core\Http\Controllers\MiPerfilController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/miPerfil', [MiPerfilController::class, 'index'])
            ->name('aplicacion.miPerfil');

        Route::get('/editarMiPerfil', [MiPerfilController::class, 'formUpdate'])
            ->name('aplicacion.miPerfil.editarMiPerfil');



        Route::put('/editarMiPerfil/actualizar', [MiPerfilController::class, 'actualizarPerfilUsuario'])
            ->name('aplicacion.miPerfil.actualizarPerfilUsuario');


        Route::post('/actualizarFotoPerfil', [MiPerfilController::class, 'actualizarFotoPerfil'])
            ->name('aplicacion.miPerfil.actualizarFotoPerfil');

        Route::post('/actualizarFotoEstablecimiento', [MiPerfilController::class, 'actualizarFotoEstablecimiento'])
            ->name('aplicacion.miPerfil.actualizarFotoEstablecimiento');
    });
});