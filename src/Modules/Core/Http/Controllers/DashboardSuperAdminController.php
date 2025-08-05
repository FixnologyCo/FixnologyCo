<?php

namespace Core\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use function Laravel\Prompts\alert;


class DashboardSuperAdminController extends Controller
{
    
    public function show($aplicacion, $rol, Request $request)
    {
        $usuario = Auth::user()->load([

            'perfilUsuario' => function ($query) {
                $query->with(['indicativo', 'tipoDocumento', 'estado', 'rol']);
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

            'establecimiento' => function ($query) {
                $query->with([
                    'token.estado',
                    'propietario',
                    'facturas' => function ($subQuery) {
                        $subQuery->with(['estado', 'medioPago']);
                    }
                ]);
            },

        ]);


        if (!in_array($usuario->perfilUsuario->rol->id, [4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        
        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Dashboard/Dashboard', [
            'usuario' => $usuario,
            
        ]);
    }

    use AuthorizesRequests;

}
