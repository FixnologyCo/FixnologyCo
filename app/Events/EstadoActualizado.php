<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <-- Importante
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstadoActualizado implements ShouldBroadcast // <-- Implementa esta interfaz
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Propiedad pública que se enviará como dato
    public $mensaje;

    public function __construct($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    // Define el canal por el que se transmitirá
    public function broadcastOn(): array
    {
        // Un canal privado para que solo usuarios autenticados escuchen
        return [new PrivateChannel('actualizaciones')];
    }

    // (Opcional) Define un nombre para el evento en el frontend
    public function broadcastAs()
    {
        return 'Estado.Actualizado';
    }
}