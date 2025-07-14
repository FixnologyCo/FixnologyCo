<?php

use Core\Http\Controllers\DashboardSuperAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    // Ruta para polling (sin parámetros)
    Route::get('/listar-clientes', [DashboardSuperAdminController::class, 'listarClientesSinParametros']);

    Route::post('/status', [DashboardSuperAdminController::class, 'status']);

    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/dashboard', [DashboardSuperAdminController::class, 'show'])
            ->name('aplicacion.dashboard');


        Route::get('/dashboard/detalle/{idCliente}', [DashboardSuperAdminController::class, 'detalle'])
            ->name('aplicacion.dashboard.detalle');

        // ✅ Nueva ruta para clientes sin activar
        Route::get('/dashboard/clientes-por-activacion', [DashboardSuperAdminController::class, 'getClientesPorActivacion'])
            ->name('clientes.activacion');

        // ✅ Ruta para eliminar cliente
        Route::delete('/clientes/{id}', [DashboardSuperAdminController::class, 'destroy'])
            ->name('clientes.destroy');

        // ✅ Ruta para actualizar cliente    
        Route::put('/clientes/{id}', [DashboardSuperAdminController::class, 'update'])->name('clientes.update');

        // ✅ Ruta para dinero activo (con prefijo de aplicación y rol)
        Route::get('/dashboard/dinero-activo', [DashboardSuperAdminController::class, 'getDineroActivo'])
            ->name('dinero.activo');

       
    });
});


