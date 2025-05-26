<?php

use Core\Http\Controllers\ClientesFixController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/clientesFix', [ClientesFixController::class, 'show'])
            ->name('aplicacion.clientesFix');
    });

});

