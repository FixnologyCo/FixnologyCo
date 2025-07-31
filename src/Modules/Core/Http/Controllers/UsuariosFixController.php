<?php

namespace Core\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UsuariosFixController extends Controller
{
    use AuthorizesRequests;

    public function show($aplicacion, $rol, Request $request)
    {
       
        $usuario = Auth::user()->load([

            'perfilUsuario' => function ($query) {
                $query->with(['rol']);
            },

            'perfilEmpleado' => function ($query) {
                $query->with(['estado', 'medioPago']);
            },

            'establecimientoAsignado' => function ($query) {
                $query->with([
                    'aplicacionWeb' => function ($subQuery) {
                        $subQuery->with(['estilo', 'estado', 'membresia.estado']);
                    }
                ]);
            },

            'establecimientos' => function ($query) {
                $query->with([
                    'token.estado',
                    'propietario',
                    'facturas' => function ($subQuery) {
                        $subQuery->with(['estado', 'medioPago']);
                    }
                ]);
            },

        ]);

        if (!in_array($usuario->perfilUsuario->rol->id, [1, 2, 4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $idEstablecimiento = 1;

        $usuariosDelEstablecimiento = User::whereHas('perfilEmpleado', function ($query) use ($idEstablecimiento) {
            $query->where('establecimiento_id', $idEstablecimiento);
        })
        ->with(['perfilUsuario.rol', 'perfilEmpleado'])
        ->get();

        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/GestorUsuarios', [
            
            'usuario' => $usuario,
            'usuariosDelEstablecimiento' => $usuariosDelEstablecimiento,
        ]);
    }

}
