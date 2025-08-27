<?php

namespace App\Http\Traits\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait ValidatesAndRedirectsUsers
{
    protected function validateAndRedirect(User $usuario)
    {
        $usuario->load('perfilUsuario.rol', 'perfilEmpleado.establecimiento.aplicacionWeb', 'perfilEmpleado.establecimiento.token');

        if ($usuario->estado_id !== 1) {
            Auth::logout();
            return redirect()->route('login.auth')->with(['error' => 'Tu cuenta está inactiva o suspendida.']);
        }

        if (!$usuario->perfilUsuario || !$usuario->perfilUsuario->rol) {
            Auth::logout();
            return redirect()->route('login.auth')->with(['error' => 'Tu usuario no tiene un perfil o rol asignado.']);
        }

        $rol = $usuario->perfilUsuario->rol;
        $nombreAplicacion = null;

        if (in_array($rol->id, [1, 2, 4])) { 
            $perfilEmpleado = $usuario->perfilEmpleado;

            if (!$perfilEmpleado || !$perfilEmpleado->establecimiento) {
                Auth::logout();
                return redirect()->route('login.auth')->with(['error' => 'No tienes un rol válido asignado en ningún establecimiento.']);
            }

            $establecimiento = $perfilEmpleado->establecimiento;

            if ($establecimiento->estado_id !== 1) {
                Auth::logout();
                return redirect()->route('login.auth')->with(['error' => 'El establecimiento al que perteneces está inactivo.']);
            }

            if ($establecimiento->token?->estado_id !== 1) {
                Auth::logout();
                return redirect()->route('login.auth')->with(['error' => 'El token de tu establecimiento está inactivo.']);
            }

            $ultimaFactura = $establecimiento->facturas()->latest('fecha_pago')->first();
            if ($ultimaFactura && $ultimaFactura->estado_id === 16) {
                Auth::logout();
                return redirect()->route('login.auth')->with('error', 'Tu establecimiento tiene un pago pendiente. Por favor, ponte al día.');
            }

            $nombreAplicacion = $establecimiento->aplicacionWeb?->nombre_app;
        } elseif ($rol->id === 3) { // Cliente
            $nombreAplicacion = 'ClienteApp';
        } else {
            Auth::logout();
            return redirect()->route('login.auth')->with(['error' => 'Tu rol no tiene un destino definido.']);
        }

        if (!$nombreAplicacion) {
            Auth::logout();
            return redirect()->route('login.auth')->with(['error' => 'No se pudo determinar la aplicación para tu rol.']);
        }

        $nombreCompletoUsuario = $usuario->perfilUsuario->primer_nombre . ' ' . $usuario->perfilUsuario->primer_apellido;

        return redirect()->route('aplicacion.dashboard', [
            'aplicacion' => $nombreAplicacion,
            'rol' => $rol->tipo_rol,
        ])->with('success', '¡Bienvenido nuevamente, ' . trim($nombreCompletoUsuario) . '!');
    }
}