<?php

namespace Core\Http\Controllers;

use Core\Models\ClienteFixgi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ConfiguracionesController extends Controller
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
            'tienda.pagosMembresia',  // Nota que "pagosMembresia" está en singular
            'estado',
            'tipoDocumento'
        ]);

        // Validar acceso con Gate (rol 4)
        if (!in_array($user->rol->id, [1, 2, 3, 4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $fotoBase64 = $user->foto_binaria
            ? 'data:image/jpeg;base64,' . base64_encode($user->foto_binaria)
            : null;


        // Verificar que el usuario tenga tienda y la aplicación coincida
        if ($user->tienda && $user->tienda->aplicacion->nombre_app === $aplicacion) {

            // Consulta principal con joins (alias renombrados para evitar conflictos con relaciones cargadas)
            $clientes = ClienteFixgi::select(
                'clientes_fixgis.id',
                \DB::raw("CONCAT(clientes_fixgis.nombres_ct, ' ', clientes_fixgis.apellidos_ct) AS nombre_completo"),
                'clientes_fixgis.telefono_ct as telefono',
                \DB::raw('COALESCE(tiendas_sistematizadas.nombre_tienda, "Sin tienda") as nombre_tienda'),
                'token_accesos.token_activacion as token',
                'aplicaciones_web.nombre_app as aplicacion',
                'membresias.nombre_membresia as membresia',
                \DB::raw('IFNULL(membresias.precio, 0) as precio'), // ✅ Reemplazo aquí
                \DB::raw('COALESCE(estados.tipo_estado, "Sin estado") as estado_tipo'),
                \DB::raw('COALESCE(token_estado.tipo_estado, "Sin estado") as estado_token'),
                'clientes_fixgis.fecha_creacion',

                // ✅ Estado, monto y fecha desde pagos_membresia
                'pagos_membresia.monto_total as monto_pago',
                'pagos_membresia.fecha_pago as fecha_pago',
                'estado_pago.tipo_estado as estado_pago'

            )
                ->leftJoin('tiendas_sistematizadas', 'clientes_fixgis.id_tienda', '=', 'tiendas_sistematizadas.id')
                ->leftJoin('token_accesos', 'tiendas_sistematizadas.id_token', '=', 'token_accesos.id')
                ->leftJoin('aplicaciones_web', 'tiendas_sistematizadas.id_aplicacion_web', '=', 'aplicaciones_web.id')
                ->leftJoin('membresias', 'aplicaciones_web.id_membresia', '=', 'membresias.id')
                ->leftJoin('estados', 'clientes_fixgis.id_estado', '=', 'estados.id')
                ->leftJoin('estados as token_estado', 'token_accesos.id_estado', '=', 'token_estado.id')

                // ✅ JOIN para los pagos de membresía
                ->leftJoin('pagos_membresia', 'clientes_fixgis.id', '=', 'pagos_membresia.id_cliente')
                ->leftJoin('estados as estado_pago', 'pagos_membresia.id_estado', '=', 'estado_pago.id')
                ->orderBy('clientes_fixgis.fecha_creacion', 'DESC')
                ->get();


            $cantidadApps = DB::table('aplicaciones_web')
                ->select(DB::raw('COUNT(DISTINCT nombre_app) as totalApps'))
                ->value('totalApps');

            $cantidadClientesRol1 = DB::table('clientes_fixgis')
                ->where('id_rol', 1)
                ->count();

            $usuariosRol4 = ClienteFixgi::where('id_rol', 4)
                ->select('id', 'nombres_ct', 'apellidos_ct') // puedes agregar 'apellidos' si necesitas
                ->get();

            $fotoBase64 = $user->foto_binaria
                ? 'data:image/webp;base64,' . $user->foto_binaria
                : null;
            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Configuraciones/Configuraciones', [
                'auth' => ['user' => $user],
                'clientes' => $clientes,
                'aplicacion' => $aplicacion,
                'rol' => $rol,

                'foto_base64' => $fotoBase64,
            ]);
        }

        abort(404);
    }

    use AuthorizesRequests;

}
