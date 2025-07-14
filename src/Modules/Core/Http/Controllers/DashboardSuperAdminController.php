<?php

namespace Core\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use function Laravel\Prompts\alert;


class  DashboardSuperAdminController extends Controller
{
    /**
     * Muestra el dashboard para la aplicación y rol especificados.
     *
     * @param  string  $aplicacion
     * @param  string  $rol
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show($aplicacion, $rol, Request $request)
    {
        $usuario = Auth::user()->load(
            'perfilUsuario',
            'perfilUsuario.indicativo',
            'perfilUsuario.tipoDocumento',
            'perfilEmpleado',
            'perfilEmpleado.estado',
            'perfilEmpleado.medioPago',
            'tienda',
            'tienda.token',
            'tienda.token.estado',
            'tienda.aplicacionWeb',
            'tienda.aplicacionWeb.estado',
            'tienda.aplicacionWeb.membresia',
            'tienda.aplicacionWeb.membresia.estado',
            'tienda.facturas',
            'tienda.facturas.estado',
            'tienda.propietario'

        );
        $tipoDeRol = $usuario->rol->tipo_rol;
        $aplicacionWeb = $usuario->tienda[0]->aplicacionWeb->nombre_app ?? null;

        
        // Validar acceso con Gate (rol 4)
        if (!in_array($usuario->rol->id, [4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

          // 5. RENDERIZAR LA VISTA DE INERTIA CON LOS PROPS CORRECTOS
        // Ya no necesitamos el 'if' que validaba la tienda, eso lo debe hacer la autorización.
            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Dashboard/Dashboard', [
                'aplicacion' => $aplicacionWeb,
                'rol' => $tipoDeRol,
                'usuario' => $usuario,
            ]);
        }
    
    use AuthorizesRequests;

}
