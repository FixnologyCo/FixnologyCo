<?php

namespace App\Http\Controllers;
use App\Models\ClienteTaurus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB; // ✅ Importa la clase DB correctamente
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\RegistraAuditoria; // 👈 Importa el trait correctamente aquí



class DashboardSuperAdminController extends Controller
{

    use RegistraAuditoria; // 👈 Usa el trait aquí a nivel de clase

    /**
     * Muestra el dashboard para la aplicación y rol especificados.
     *
     * @param  string  $aplicacion
     * @param  string  $rol
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */



    public function listarClientes($aplicacion, $rol, Request $request)
    {
        $user = auth()->user()->load(['rol', 'tienda.aplicacion']);

        // Validación temprana
        if (!$user->tienda || $user->tienda->aplicacion->nombre_app !== $aplicacion) {
            return response()->json([], 403);
        }

        // Consulta principal
        $clientes = ClienteTaurus::select([
            'clientes_taurus.id',
            \DB::raw("CONCAT(clientes_taurus.nombres_ct, ' ', clientes_taurus.apellidos_ct) AS nombre_completo"),
            'clientes_taurus.telefono_ct as telefono',
            'tiendas_sistematizadas.nombre_tienda',
            'token_accesos.token_activacion as token',
            'aplicaciones_web.nombre_app as aplicacion',
            'membresias.nombre_membresia as membresia',
            \DB::raw('IFNULL(membresias.precio, 0) as precio'),
            \DB::raw('COALESCE(estados.tipo_estado, "Sin estado") as estado_tipo'),
            \DB::raw('COALESCE(token_estado.tipo_estado, "Sin estado") as estado_token'),
            'clientes_taurus.fecha_creacion',
            'pagos_membresia.monto_total as monto_pago',
            'pagos_membresia.fecha_pago as fecha_pago',
            'estado_pago.tipo_estado as estado_pago',
        ])
            ->leftJoin('tiendas_sistematizadas', 'clientes_taurus.id_tienda', '=', 'tiendas_sistematizadas.id')
            ->leftJoin('token_accesos', 'tiendas_sistematizadas.id_token', '=', 'token_accesos.id')
            ->leftJoin('aplicaciones_web', 'tiendas_sistematizadas.id_aplicacion_web', '=', 'aplicaciones_web.id')
            ->leftJoin('membresias', 'aplicaciones_web.id_membresia', '=', 'membresias.id')
            ->leftJoin('estados', 'clientes_taurus.id_estado', '=', 'estados.id')
            ->leftJoin('estados as token_estado', 'token_accesos.id_estado', '=', 'token_estado.id')
            ->leftJoin('pagos_membresia', 'clientes_taurus.id', '=', 'pagos_membresia.id_cliente')
            ->leftJoin('estados as estado_pago', 'pagos_membresia.id_estado', '=', 'estado_pago.id')
            ->orderByDesc('clientes_taurus.fecha_creacion')
            ->get();

        return response()->json($clientes);
    }


    // Este método es para polling sin parámetros dinámicos
    public function listarClientesSinParametros()
    {
        $user = auth()->user()->load(['rol', 'tienda.aplicacion']);

        if (!in_array($user->rol->id, [1, 2, 3, 4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        if ($user->tienda && $user->tienda->aplicacion) {
            $clientes = ClienteTaurus::select(
                'clientes_taurus.id',
                \DB::raw("CONCAT(clientes_taurus.nombres_ct, ' ', clientes_taurus.apellidos_ct) AS nombre_completo"),
                'clientes_taurus.telefono_ct as telefono',
                \DB::raw('COALESCE(tiendas_sistematizadas.nombre_tienda, "Sin tienda") as nombre_tienda'),
                'token_accesos.token_activacion as token',
                'aplicaciones_web.nombre_app as aplicacion',
                'membresias.nombre_membresia as membresia',
                \DB::raw('IFNULL(membresias.precio, 0) as precio'),
                \DB::raw('COALESCE(estados.tipo_estado, "Sin estado") as estado_tipo'),
                \DB::raw('COALESCE(token_estado.tipo_estado, "Sin estado") as estado_token'),
                'clientes_taurus.fecha_creacion',
                'pagos_membresia.monto_total as monto_pago',
                'pagos_membresia.fecha_pago as fecha_pago',
                'estado_pago.tipo_estado as estado_pago'
            )
                ->leftJoin('tiendas_sistematizadas', 'clientes_taurus.id_tienda', '=', 'tiendas_sistematizadas.id')
                ->leftJoin('token_accesos', 'tiendas_sistematizadas.id_token', '=', 'token_accesos.id')
                ->leftJoin('aplicaciones_web', 'tiendas_sistematizadas.id_aplicacion_web', '=', 'aplicaciones_web.id')
                ->leftJoin('membresias', 'aplicaciones_web.id_membresia', '=', 'membresias.id')
                ->leftJoin('estados', 'clientes_taurus.id_estado', '=', 'estados.id')
                ->leftJoin('estados as token_estado', 'token_accesos.id_estado', '=', 'token_estado.id')
                ->leftJoin('pagos_membresia', 'clientes_taurus.id', '=', 'pagos_membresia.id_cliente')
                ->leftJoin('estados as estado_pago', 'pagos_membresia.id_estado', '=', 'estado_pago.id')
                ->orderBy('clientes_taurus.fecha_creacion', 'DESC')
                ->get();

            return response()->json($clientes);
        }

        return response()->json([], 403);
    }

    public function getClientesPorActivacion($aplicacion, $rol)
    {
        $clientes = ClienteTaurus::select(
            'clientes_taurus.id',
            'clientes_taurus.nombres_ct',
            'clientes_taurus.apellidos_ct',
            'tiendas_sistematizadas.nombre_tienda'
        )
            ->join('tiendas_sistematizadas', 'clientes_taurus.id_tienda', '=', 'tiendas_sistematizadas.id')
            ->join('token_accesos', 'tiendas_sistematizadas.id_token', '=', 'token_accesos.id')
            ->where('token_accesos.id_estado', 2) // ✅ Filtrar por id_estado = 2
            ->get();

        return response()->json($clientes);
    }


    // Método que retorna si hay cambios desde el último cliente
    public function status(Request $request)
    {
        $lastId = $request->input('lastId', 0); // ID que envía el frontend

        $cliente = ClienteTaurus::latest('id')->first();

        if ($cliente && $cliente->id > $lastId) {
            return response()->json([
                'update' => true,
                'newId' => $cliente->id,
            ]);
        }

        return response()->json([
            'update' => false,
        ]);
    }


    // Agrega este método al DashboardController
    public function detalle($aplicacion, $rol, $idCliente)
    {
        // Opcional: valida permisos (rol 4, etc.)
        $user = auth()->user()->load([
            'rol',
            'tienda',
            'tienda.aplicacion',
        ]);
        if (!Gate::allows('access-role', 4) || $user->rol->id != 4) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        // Cargar el cliente con todas las relaciones necesarias
        $detalleCliente = ClienteTaurus::with([
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
            'tipoDocumento',
            'membresia'
        ])->findOrFail($idCliente);

        // Retornar en JSON (para usar en el modal)
        return response()->json($detalleCliente);
    }


    public function getDineroActivo()
    {
        $dineroActivo = DB::table('pagos_membresia')
            ->join('tiendas_sistematizadas', 'pagos_membresia.id_tienda', '=', 'tiendas_sistematizadas.id')
            ->join('aplicaciones_web', 'tiendas_sistematizadas.id_aplicacion_web', '=', 'aplicaciones_web.id')
            ->where('pagos_membresia.id_estado', 8)
            ->where('pagos_membresia.dias_restantes', '>', 0)
            ->where('aplicaciones_web.id', '<>', 1)
            ->sum('pagos_membresia.monto_total');

        return response()->json([
            'total_activo' => $dineroActivo,
        ]);
    }

    use AuthorizesRequests;


    public function destroy(Request $request, $aplicacion, $rol, $id)
    {
        $cliente = ClienteTaurus::with([
            'rol',
            'tienda.token',
            'tienda.pagosMembresia',
            'tienda.membresia.detallesPlan',
            'pagosMembresia'
        ])->find($id);

        if (!$cliente) {
            return redirect()->route('aplicacion.dashboard', [
                'aplicacion' => $aplicacion,
                'rol' => ucfirst($rol)
            ])->with('error', 'Cliente no encontrado.');
        }

        $tienda = $cliente->tienda;
        $mensaje = 'Cliente eliminado exitosamente.'; // ✅ Mensaje por defecto

        // 1. Eliminar pagos de membresía asociados al cliente
        $cliente->pagosMembresia()->delete();

        // 2. Eliminar el cliente
        $cliente->deleteOrFail();

        // 3. Verificar si quedan otros clientes en la misma tienda
        if ($tienda && $tienda->clientesTaurus()->count() === 0) {
            // Eliminar pagos de membresía de la tienda
            $tienda->pagosMembresia()->delete();

            // Eliminar token
            if ($tienda->token) {
                $tienda->token()->delete();
            }

            // Eliminar detalles del plan si existen
            if ($tienda->membresia && $tienda->membresia->detallesPlan) {
                $tienda->membresia->detallesPlan()->delete();
            }

            // Eliminar membresía
            if ($tienda->membresia) {
                $tienda->membresia()->delete();
            }

            // Eliminar tienda
            $tienda->delete();

            $mensaje2 = ' También se eliminó la tienda ya que no tenía más clientes.'; // ✅ Mensaje extendido
        }

        // Auditoría
        $this->registrarAuditoria(
            'Eliminado',
            'ClienteTaurus',
            $cliente->numero_documento_ct,
            'Eliminación de cliente Taurus',
            ['evento' => 'Eliminación de cliente taurus']
        );

        $nombreRol = $cliente->rol ? $cliente->rol->nombre_rol : 'SuperAdmin';

        return redirect()->route('aplicacion.dashboard', [
            'aplicacion' => $aplicacion,
            'rol' => ucfirst($nombreRol ?: 'SuperAdmin')
        ])->with('success', $mensaje);
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


        if ($user->tienda && $user->tienda->aplicacion->nombre_app === $aplicacion) {

            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Dashboard/Dashboard', [
                'auth' => ['user' => $user],
                'aplicacion' => $aplicacion,
                'rol' => $rol,
                'detalles' => $user->tienda->aplicacion->plan->detalles,
            ]);
        }

        abort(404);
    }
}
