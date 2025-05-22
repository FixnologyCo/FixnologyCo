<?php

namespace Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Controller
{
    public function validarAcceso($aplicacion, $rol, Request $request, array $rolesPermitidos)
    {
        $user = auth()->user()->load([
            'rol',
            'tienda.aplicacion.membresia.estado',
            'tienda.pagosMembresia',
        ]);

        // Verifica si el rol está permitido
        if (!in_array($user->rol->id, $rolesPermitidos)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.auth')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Verifica que la tienda exista y que el nombre coincida
        if (!$user->tienda || $user->tienda->aplicacion->nombre_app !== $aplicacion) {
            abort(404);
        }

        return null;
    }
}
