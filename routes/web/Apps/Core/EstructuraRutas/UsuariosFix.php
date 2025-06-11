<?php

use Core\Http\Controllers\ClientesFixController;
use Core\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/GestorUsuarios', [UsuariosController::class,'show'])
            ->name('aplicacion.GestorUsuarios');

        Route::get('/clientesFix', [ClientesFixController::class, 'show'])
            ->name('aplicacion.clientesFix');
    });

});

