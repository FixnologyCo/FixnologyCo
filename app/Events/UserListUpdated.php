<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserListUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * El mensaje o datos que quieres enviar (opcional).
     * @var string
     */
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        $this->message = "La lista de usuarios ha sido actualizada.";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Usamos un canal privado para que solo los usuarios autenticados puedan escucharlo.
        // El nombre 'user-updates' es un ejemplo, puedes usar el que prefieras.
        return [
            new PrivateChannel('UsuariosActualizados'),
        ];
    }

    /**
     * El nombre con el que se transmitirá el evento.
     * Por defecto, sería 'UserListUpdated', pero así es más explícito.
     */
    public function broadcastAs()
    {
        return 'ListaUsuariosActualizados';
    }
}
