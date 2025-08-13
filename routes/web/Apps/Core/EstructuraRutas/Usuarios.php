<?php

use Core\Http\Controllers\UsuariosFixController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::prefix('{aplicacion}/{rol}')->group(function () {
        Route::get('/gestorUsuarios', action: [UsuariosFixController::class, 'show'])
            ->name('aplicacion.gestorUsuarios');
    });
    // routes/web.php

// routes/web.php

// Asegúrate de que estas rutas estén dentro de un grupo con middleware de autenticación

// Ruta para enviar a la papelera (Soft Delete)
Route::delete('/usuarios/{usuarioAEliminar}', [UsuariosFixController::class, 'destroy'])->name('usuarios.destroy');

// Ruta para ver la papelera
Route::get('/{aplicacion}/{rol}/usuarios/papelera', [UsuariosFixController::class, 'trash'])->name('usuarios.trash');

// Ruta para restaurar un usuario
// Usamos {id} en lugar de route model binding directo para encontrarlo con withTrashed()
Route::post('/usuarios/{id}/restaurar', [UsuariosFixController::class, 'restore'])->name('usuarios.restore');

// Ruta para eliminar permanentemente
Route::delete('/usuarios/{id}/permanente', [UsuariosFixController::class, 'forceDestroy'])->name('usuarios.forceDestroy');

Route::post('/usuarios/papelera/vaciar', [UsuariosFixController::class, 'emptyTrash'])->name('usuarios.emptyTrash');
// routes/web.php
Route::post('/usuarios', [UsuariosFixController::class, 'store'])->name('usuarios.store');
// routes/web.php (dentro del grupo de middleware 'auth')
Route::post('/usuarios/bulk-destroy', [UsuariosFixController::class, 'bulkDestroy'])->name('usuarios.bulkDestroy');
});


