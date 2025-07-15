<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Laravel\Socialite\Facades\Socialite;

$routesPath = __DIR__ . '/web';
foreach (File::allFiles($routesPath) as $routeFile) {
    require_once $routeFile->getPathname();
}
Route::get('/login', function () {
    return redirect()->route('login.auth');
})->name('login');

// En routes/web.php
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

use App\Events\EstadoActualizado;

Route::get('/test-broadcast', function () {
    broadcast(new EstadoActualizado('¡El estado de algo ha cambiado!'))->toOthers();
    return "¡Evento enviado!";
})->name('test-broadcast');

