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

class UsuariosSuperAdminController extends Controller
{
    /**
     * Muestra el dashboard para la aplicación y rol especificados.
     *
     * @param  string  $aplicacion
     * @param  string  $rol
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse|null
     */

    public function show($aplicacion, $rol, Request $request)
    {
        if ($respuesta = $this->validarAcceso($aplicacion, $rol, $request, [4])) {
            return $respuesta;
        }

        $user = auth()->user()->first();

        $fotoBase64 = $user->foto_binaria
            ? 'data:image/jpeg;base64,' . $user->foto_binaria
            : null;

        $empleadosFix = ClienteFixgi::where('id_rol', 4)
            ->select(
                'clientes_fixgis.id',
                'clientes_fixgis.foto_binaria',
                'clientes_fixgis.nombres_ct',
                'clientes_fixgis.apellidos_ct',
                'clientes_fixgis.telefono_ct',
                'clientes_fixgis.email_ct',
                'tiendas_sistematizadas.nombre_tienda',
                'clientes_fixgis.fecha_creacion',
                'estados.tipo_estado',
                \DB::raw('COALESCE(token_estado.tipo_estado, "Sin estado") as estado_token'),
                'aplicaciones_web.nombre_app'
            )
            ->leftJoin('tiendas_sistematizadas', 'clientes_fixgis.id_tienda', '=', 'tiendas_sistematizadas.id')
            ->leftJoin('estados', 'clientes_fixgis.id_estado', '=', 'estados.id')

            // 🔽 Asegúrate de unir token_accesos primero
            ->leftJoin('token_accesos', 'tiendas_sistematizadas.id', '=', 'token_accesos.id')

            // 🔽 Ahora puedes usar token_accesos.id_estado
            ->leftJoin('estados as token_estado', 'token_accesos.id_estado', '=', 'token_estado.id')

            ->leftJoin('aplicaciones_web', 'tiendas_sistematizadas.id_aplicacion_web', '=', 'aplicaciones_web.id')
            ->orderBy('clientes_fixgis.fecha_creacion', 'DESC')
            ->get()
            ->map(function ($cliente) {
                $cliente->foto_cliente = $cliente->foto_binaria
                    ? 'data:image/jpeg;base64,' . $cliente->foto_binaria
                    : null;
                return $cliente;
            });



        $clientesFix = ClienteFixgi::whereIn('id_rol', [1, 2, 3])
            ->select(
                'clientes_fixgis.id',
                'clientes_fixgis.foto_binaria',
                'clientes_fixgis.nombres_ct',
                'clientes_fixgis.apellidos_ct',
                'clientes_fixgis.telefono_ct',
                'clientes_fixgis.email_ct',
                'tiendas_sistematizadas.nombre_tienda',
                'clientes_fixgis.fecha_creacion',
                'estados.tipo_estado',
                \DB::raw('COALESCE(token_estado.tipo_estado, "Sin estado") as estado_token'),
                'aplicaciones_web.nombre_app'
            )
            ->leftJoin('tiendas_sistematizadas', 'clientes_fixgis.id_tienda', '=', 'tiendas_sistematizadas.id')
            ->leftJoin('estados', 'clientes_fixgis.id_estado', '=', 'estados.id')

            // 🔽 Asegúrate de unir token_accesos primero
            ->leftJoin('token_accesos', 'tiendas_sistematizadas.id', '=', 'token_accesos.id')

            // 🔽 Ahora puedes usar token_accesos.id_estado
            ->leftJoin('estados as token_estado', 'token_accesos.id_estado', '=', 'token_estado.id')

            ->leftJoin('aplicaciones_web', 'tiendas_sistematizadas.id_aplicacion_web', '=', 'aplicaciones_web.id')
            ->orderBy('clientes_fixgis.fecha_creacion', 'DESC')
            ->get()
            ->map(function ($cliente) {
                $cliente->foto_cliente = $cliente->foto_binaria
                    ? 'data:image/jpeg;base64,' . $cliente->foto_binaria
                    : null;
                return $cliente;
            });



        if ($user->tienda && $user->tienda->aplicacion->nombre_app === $aplicacion) {

            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/GestorUsuarios', [
                'auth' => ['user' => $user],
                'aplicacion' => $aplicacion,
                'rol' => $rol,
                'foto_base64' => $fotoBase64,
                'clientesFix' => $clientesFix,
                'empleadosFix' => $empleadosFix,

            ]);
        }

        abort(404);
    }

    public function detailsClienteFixgi($aplicacion, $rol, Request $request, $id)
    {

        if ($respuesta = $this->validarAcceso($aplicacion, $rol, $request, [4])) {
            return $respuesta;
        }

        $user = auth()->user()->first();

        $fotoBase64 = $user->foto_binaria
            ? 'data:image/jpeg;base64,' . $user->foto_binaria
            : null;

        $detallesCliente = ClienteFixgi::with('tienda', 'estado') // Ajusta según tus relaciones
            ->findOrFail($id);

            $fotoUser = $detallesCliente->foto_binaria
            ? 'data:image/jpeg;base64,' . $detallesCliente->foto_binaria
            : null;

            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/DetallesUsuario', [
                'auth' => ['user' => $user],
                'aplicacion' => $aplicacion,
                'rol' => $rol,
                'foto_base64' => $fotoBase64,
                'detallesCliente' => $detallesCliente,
                'foto_binaria' => $fotoUser

            ]);
        }


    public function updateClienteFixgi(Request $request, $id)
    {
    }

    public function destroyClienteFixgi(Request $request, $id)
    {
    }
    use AuthorizesRequests;

}
