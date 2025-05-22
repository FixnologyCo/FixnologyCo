<?php

use App\Http\Controllers\CrearClienteFixgiController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/crearClientes', [CrearClienteFixgiController::class, 'index'])
            ->name('aplicacion.crearClientes');
    });
});


