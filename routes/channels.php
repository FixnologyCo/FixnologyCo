
<?php
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aquí puedes registrar todos los canales de eventos que tu aplicación soporta.
| El archivo es cargado automáticamente por Laravel.
|
*/

// Ejemplo de canal privado para usuarios autenticados
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});