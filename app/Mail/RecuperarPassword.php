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
    public $nombre;

    public function __construct($token, $correo, $nombre)
    {
        $this->token = $token;
        $this->correo = $correo;
        $this->nombre = $nombre;
    }


    public function build()
    {
        $url = url("/linkRecuperacion/reset-password/{$this->token}?email={$this->correo}");
        $pathToImage = public_path('images/logo.png');
        return $this->subject('Recuperar contraseña - Fixnology')
            ->view('emails.recuperar-password')
            ->with([
                'url' => $url,
                'nombre' => $this->nombre,
                'pathToImage' => $pathToImage, 
            ]);
    }

}
