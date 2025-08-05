<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Redirige al usuario a su dashboard específico basado en su rol y aplicación.
     */
    public function index()
    {
        $usuario = Auth::user()->load(['perfilUsuario.rol', 'establecimientoAsignado.aplicacionWeb']);

        // Asegúrate de que estas rutas de datos sean correctas para tu estructura
        $aplicacion = $usuario->establecimientoAsignado->aplicacionWeb->nombre_app ?? 'defaultApp';
        $rol = $usuario->perfilUsuario->rol->tipo_rol ?? 'defaultRole';

        // Redirige a la ruta del dashboard dinámico con los parámetros correctos
        return redirect()->route('aplicacion.dashboard', [
            'aplicacion' => $aplicacion,
            'rol' => $rol,
        ]);
    }
}
