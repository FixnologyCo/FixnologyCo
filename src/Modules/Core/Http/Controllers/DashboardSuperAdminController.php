<?php

namespace Core\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\ClienteFixgi;
use Core\Models\AplicacionWeb;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB; // ✅ Importa la clase DB correctamente
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\RegistraAuditoria; // 👈 Importa el trait correctamente aquí



class DashboardSuperAdminController extends Controller
{

    use AuthorizesRequests;

    /**
     * Muestra el dashboard para la aplicación y rol especificados.
     *
     * @param  string  $aplicacion
     * @param  string  $rol
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */



    use RegistraAuditoria;
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

            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Dashboard/Dashboard', [
                'auth' => ['user' => $user],
                'aplicacion' => $aplicacion,
                'rol' => $rol,
                'foto_base64' => $fotoBase64,

            ]);
        }

        abort(404);
    }



}
