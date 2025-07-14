<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PruebaCorreo;

class EnviarCorreoPrueba extends Command
{
    protected $signature = 'correo:prueba';
    protected $description = 'Envía un correo de prueba al correo configurado';

    public function handle()
    {
        $correoDestino = 'sebastianzamudio2004@gmail.com';

        Mail::to($correoDestino)->send(new PruebaCorreo('📬 Este es un correo de prueba con un nuevo nombre de usuario'));

        $this->info('📬 Correo de prueba enviado exitosamente a ' . $correoDestino);
    }
}
