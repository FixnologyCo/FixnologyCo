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
                $query->with(['rol', 'estado', 'indicativo']);
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
            ->with([
                'perfilUsuario' => function ($query) {
                    $query->with(['rol', 'estado', 'indicativo']);
                },
                // ¡AQUÍ ESTÁ EL CAMBIO CLAVE!
                'perfilEmpleado' => function ($query) {
                    // Cargamos el establecimiento Y sus facturas a través del perfil del empleado
                    $query->with(['estado', 'medioPago', 'establecimientos.facturas.estado']);
                },
                // Se elimina la relación 'establecimientos' que causa el conflicto
            ])
            ->get();
        // 1. Obtener los IDs de usuarios con sesión activa
        $activeSessionUserIds = DB::table(config('session.table'))
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique();

        $usuariosDelEstablecimiento->map(function ($usuario) use ($activeSessionUserIds) {
            $usuario->tiene_sesion_activa = $activeSessionUserIds->contains($usuario->id);
            return $usuario;
        });

        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/GestorUsuarios', [

            'usuario' => $usuario,
            'usuariosDelEstablecimiento' => $usuariosDelEstablecimiento,
        ]);
    }


}
