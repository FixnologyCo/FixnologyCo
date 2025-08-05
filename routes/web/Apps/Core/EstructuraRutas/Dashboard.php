<?php

use Core\Http\Controllers\DashboardSuperAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/dashboard', [DashboardSuperAdminController::class, 'show'])
            ->name('aplicacion.dashboard');
    });
});


