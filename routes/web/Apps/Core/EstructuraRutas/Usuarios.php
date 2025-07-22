<?php

use Core\Http\Controllers\UsuariosFixController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/gestorUsuarios', action: [UsuariosFixController::class, 'show'])
            ->name('aplicacion.gestorUsuarios');
    });
});


