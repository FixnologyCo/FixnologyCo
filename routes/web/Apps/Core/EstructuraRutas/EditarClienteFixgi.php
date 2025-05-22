<?php

use Core\Http\Controllers\EditarClienteFixgiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/editarClienteFixgi', [EditarClienteFixgiController::class, 'index'])
            ->name('aplicacion.clientesTaurus');

        Route::get('/editarClienteFixgi/{id}', [EditarClienteFixgiController::class, 'editar'])
            ->name('aplicacion.editarClienteFixgi.id');

        Route::put('/editarClienteFixgi/{id}', [EditarClienteFixgiController::class, 'actualizar'])
            ->name('aplicacion.editarClienteFixgi.actualizar');
    });

});

