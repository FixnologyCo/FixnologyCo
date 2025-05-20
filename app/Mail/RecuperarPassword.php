<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecuperarPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $correo;

    public function __construct($token, $correo)
    {
        $this->token = $token;
        $this->correo = $correo;
    }


   public function build()
{
    $url = url("/linkRecuperacion/reset-password/{$this->token}?email={$this->correo}");

    return $this->subject('Recuperar contraseña - Fixnology')
        ->view('emails.recuperar-password')
        ->with([
            'url' => $url,
        ]);
}

}
