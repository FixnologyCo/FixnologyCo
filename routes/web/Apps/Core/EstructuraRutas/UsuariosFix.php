<?php

use Core\Http\Controllers\ClientesFixController;
use Core\Http\Controllers\UsuariosSuperAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/GestorUsuarios', [UsuariosSuperAdminController::class, 'show'])
            ->name('aplicacion.GestorUsuarios');

        Route::get('DetallesUsuario/{id}', [UsuariosSuperAdminController::class, 'detailsClienteFixgi'])->name('aplicacion.detallesUsuarios.id');

        Route::post('activar-token/{id}', [UsuariosSuperAdminController::class, 'activarToken'])
            ->name('aplicacion.activarToken');



    });

});

