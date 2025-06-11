<?php

namespace Core\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use Core\Models\ClienteFixgi;
use Core\Models\AplicacionWeb;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Traits\RegistraAuditoria;

class UsuariosController extends Controller
{
    /**
     * Muestra el dashboard para la aplicación y rol especificados.
     *
     * @param  string  $aplicacion
     * @param  string  $rol
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index($aplicacion, $rol, Request $request)
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
            'estado',
            'tipoDocumento'
        ]);

        // Validar acceso con Gate (rol 4)
        if (!in_array($user->rol->id, [1, 2, 3, 4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        // Verificar que el usuario tenga tienda y la aplicación coincida
        if ($user->tienda && $user->tienda->aplicacion->nombre_app === $aplicacion) {


            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/Usuarios', [
                'auth' => ['user' => $user],
                'aplicacion' => $aplicacion,
                'rol' => $rol,
            ]);



        }

        abort(404);
    }


    public function show($aplicacion, $rol, Request $request)
    {

        if ($respuesta = $this->validarAcceso($aplicacion, $rol, $request, [4])) {
            return $respuesta;
        }

        $user = auth()->user()->load([
            'tienda.aplicacion.membresia.estado',
            'tienda.pagosMembresia',
        ]);



        $cantidadApps = DB::table('aplicaciones_web')
            ->select(DB::raw('COUNT(DISTINCT nombre_app) as totalApps'))
            ->value('totalApps');

        $cantidadClientesRol1 = DB::table('clientes_fixgis')
            ->where('id_rol', 1)
            ->count();

        $usuariosRol4 = ClienteFixgi::where('id_rol', 4)
            ->select('id', 'nombres_ct', 'apellidos_ct')
            ->get();

            $fotoBase64 = $user->foto_binaria
                ? 'data:image/jpeg;base64,' . $user->foto_binaria
                : null;

        if ($user->tienda && $user->tienda->aplicacion->nombre_app === $aplicacion) {

            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/GestorUsuarios', [
                'auth' => ['user' => $user],
                'aplicacion' => $aplicacion,
                'rol' => $rol,
                'foto_base64' => $fotoBase64,

            ]);
        }

        abort(404);
    }


    public function updateClienteFixgi(Request $request, $id)
    {
    }

    public function destroyClienteFixgi(Request $request, $id)
    {
    }
    use AuthorizesRequests;

}
