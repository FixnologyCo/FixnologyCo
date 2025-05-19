<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecuperarPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        $url = url('/reset-password/' . $this->token);
        return $this->subject('Restablece tu contraseña')
                    ->view('emails.reset_password')
                    ->with(['url' => $url]);
    }
}
