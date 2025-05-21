<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PruebaCorreo extends Mailable
{
    use Queueable, SerializesModels;

    public $mensaje;

    public function __construct($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this->subject('Correo de prueba desde FixnologyAPP')
                    ->view('emails.prueba');
    }
}
