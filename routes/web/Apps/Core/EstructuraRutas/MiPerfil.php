<?php

use Core\Http\Controllers\MiPerfilController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/miPerfil', [MiPerfilController::class, 'index'])
            ->name('aplicacion.miPerfil');

        Route::get('/editarMiPerfil', [MiPerfilController::class, 'formUpdate'])
            ->name('aplicacion.miPerfil.editarMiPerfil');

        // Ruta PUT para actualizar los datos del formulario (nombre, email, etc.)
        Route::put('/editarMiPerfil', [MiPerfilController::class, 'actualizar'])
            ->name('aplicacion.editarMiPerfil.actualizar');

        // ¡NUEVA RUTA! POST para actualizar SOLO la foto de perfil
        Route::post('/update-profile-photo', [MiPerfilController::class, 'updateProfilePhoto'])
            ->name('aplicacion.miPerfil.updatePhoto');
    });
});