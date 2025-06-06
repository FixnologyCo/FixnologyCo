<?php

namespace Core\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\AplicacionWeb;
use App\Models\ClienteFixgi;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AplicacionWebController extends Controller
{
    public function index()
    {
        $aplicaciones = AplicacionWeb::where('id_membresia', 1)->get();

        return Inertia::render('Core/Auth/Registro', [
            'aplicaciones' => $aplicaciones
        ]);
    }


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
        // Cargar relaciones necesarias del usuario autenticado
        $user = auth()->user()->load([
            'rol',
            'tienda',
            'tienda.token',
            'tienda.token.estado',
            'tienda.estado',
            'tienda.aplicacion',
            'tienda.aplicacion.plan',
            'tienda.aplicacion.plan.detalles',
            'tienda.aplicacion.membresia',
            'tienda.aplicacion.membresia.estado',
            'tienda.pagosMembresia',
            'estado',
            'tipoDocumento'
        ]);

        // Validar acceso con Gate (rol 4)
        if (!in_array($user->rol->id, [4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        // Verificar que el usuario tenga tienda y la aplicación coincida
        if ($user->tienda && $user->tienda->aplicacion->nombre_app === $aplicacion) {

            // Obtener la tienda del usuario logueado
            $idTienda = $user->tienda->id;

            // Obtener los IDs de los usuarios que pertenecen a esa tienda
            $userIds = ClienteFixgi::where('id_tienda', $idTienda)->pluck('id');


            $fotoBase64 = $user->foto_binaria
                ? 'data:image/jpeg;base64,' . $user->foto_binaria
                : null;

            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/MisApps/MisApps', [
                'auth' => ['user' => $user],
                'aplicacion' => $aplicacion,
                'rol' => $rol,
                'foto_base64' => $fotoBase64,
            ]);

        }

        abort(404);
    }

    use AuthorizesRequests;

}


