<?php

namespace Core\Http\Controllers;

use App\Models\User;
use Core\Models\PerfilEmpleado;
use Core\Models\PerfilUsuario;
use Core\Models\FacturacionMembresias;
use Illuminate\Support\Facades\Auth; // Es buena práctica usar el Facade directamente
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use Core\Models\Establecimientos;
use Core\Models\TokensAcceso;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Events\UserListUpdated;

class UsuariosFixController extends Controller
{
    use AuthorizesRequests;

    public function show($aplicacion, $rol, Request $request)
    {
        $usuarioAutenticado = Auth::user()->load(['perfilUsuario.rol', 'establecimientoAsignado.aplicacionWeb.estilo']);

        if (!in_array($usuarioAutenticado->perfilUsuario->rol->id, [4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        // --- 1. DEFINIMOS LAS RELACIONES A CARGAR UNA SOLA VEZ (LA CLAVE DE LA CONSISTENCIA) ---
        $relationsToLoad = [
            'perfilUsuario' => fn($q) => $q->with(['indicativo', 'tipoDocumento', 'estado', 'rol']),
            'perfilEmpleado' => fn($q) => $q->with(['estado', 'medioPago']),
            'establecimientoAsignado.aplicacionWeb.membresia.estado',
            'establecimiento' => fn($q) => $q->with(['token.estado', 'propietario', 'facturas.estado', 'facturas.medioPago']),
        ];

        // --- 2. OBTENEMOS TODAS LAS LISTAS USANDO LAS MISMAS RELACIONES ---
        $idEstablecimiento = $usuarioAutenticado->establecimientoAsignado->id ?? null;

        $misEmpleados = $idEstablecimiento ? User::whereHas('perfilEmpleado', fn($q) => $q->where('establecimiento_id', $idEstablecimiento))
            ->where('id', '!=', $usuarioAutenticado->id)
            ->with($relationsToLoad)
            ->get() : collect();

        $todosLosUsuarios = User::with($relationsToLoad)->get();
        $usuariosEnPapelera = User::onlyTrashed()->with($relationsToLoad)->get();

        // Para el selector, solo necesitamos la lista básica de establecimientos
        $establecimientosDisponibles = Establecimientos::all();

        // --- 3. LÓGICA DE SESIÓN ACTIVA ---
        $activeSessionUserIds = DB::table(config('session.table'))
            ->whereNotNull('user_id')->pluck('user_id')->unique();

        $todosLosUsuarios->each(fn($u) => $u->tiene_sesion_activa = $activeSessionUserIds->contains($u->id));
        $misEmpleados->each(fn($u) => $u->tiene_sesion_activa = $activeSessionUserIds->contains($u->id));

        // --- 4. DEVOLVEMOS LOS DATOS CRUDOS A INERTIA ---
        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/GestorUsuarios', [
            'usuario' => $usuarioAutenticado,
            'misEmpleados' => $misEmpleados,
            'todosLosUsuarios' => $todosLosUsuarios,
            'usuariosEnPapelera' => $usuariosEnPapelera,
            'establecimientosDisponibles' => $establecimientosDisponibles,
        ]);
    }

    /**
     * Elimina permanentemente un usuario y todos sus datos asociados.
     *
     * @param  \App\Models\User  $usuarioAEliminar
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Envía un usuario a la papelera (Soft Delete).
     */
    public function destroy(User $usuarioAEliminar)
    {
        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            abort(403, 'No tienes permisos para esta acción.');
        }
        if ($usuarioAEliminar->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // El método delete() ahora ejecutará un Soft Delete gracias al trait
        $usuarioAEliminar->delete();

        return redirect()->back()->with('success', 'Usuario enviado a la papelera.');
    }

    /**
     * Muestra los usuarios que están en la papelera.
     */
    public function trash($aplicacion, $rol)
    {
        $usuarioAutenticado = Auth::user()->load(['perfilUsuario.rol']);

        // Usamos onlyTrashed() para obtener solo los registros eliminados suavemente
        $usuariosEnPapelera = User::onlyTrashed()
            ->with([/* Carga las mismas relaciones que en 'show' para consistencia */])
            ->get();

        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/Papelera', [
            'usuario' => $usuarioAutenticado,
            'usuariosEnPapelera' => $usuariosEnPapelera,
        ]);
    }

    /**
     * Restaura un usuario desde la papelera.
     */
    public function restore($id)
    {
        // Usamos withTrashed() para encontrar el usuario aunque esté en la papelera
        $usuarioARestaurar = User::withTrashed()->findOrFail($id);

        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            abort(403, 'No tienes permisos para esta acción.');
        }

        // El método restore() quitará la fecha de 'deleted_at'
        $usuarioARestaurar->restore();


        return redirect()->back()->with('success', 'Usuario restaurado correctamente.');
    }

    /**
     * Elimina permanentemente un usuario de la base de datos.
     */
    public function forceDestroy($id)
    {
        // Busca al usuario, incluso si ya está en la papelera
        $usuarioAEliminar = User::withTrashed()->findOrFail($id);

        // --- Autorización ---
        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            abort(403, 'No tienes permisos para esta acción.');
        }
        if ($usuarioAEliminar->id === Auth::id()) {
            return back()->with('error', 'Acción no permitida.');
        }

        try {
            DB::beginTransaction();

            // Carga las relaciones principales del usuario que se va a eliminar
            $usuarioAEliminar->load(['perfilUsuario', 'perfilEmpleado', 'establecimiento']);

            // --- LÓGICA DE ELIMINACIÓN EN CASCADA ---

            // PASO 1: Eliminar dependencias directas del usuario como cliente
            FacturacionMembresias::where('cliente_id', $usuarioAEliminar->id)->forceDelete();

            // PASO 2: Si el usuario es propietario de un establecimiento...
            if ($establecimiento = $usuarioAEliminar->establecimiento) {

                // a) Eliminar a todos los demás empleados de este establecimiento.
                PerfilEmpleado::where('establecimiento_id', $establecimiento->id)
                    ->where('usuario_id', '!=', $usuarioAEliminar->id)
                    ->forceDelete();

                // b) Eliminar los datos del establecimiento.
                $establecimiento->facturas()->forceDelete();
                $establecimiento->token()->forceDelete();

                // c) Finalmente, eliminar el establecimiento.
                $establecimiento->forceDelete();
            }

            // PASO 3: Eliminar los perfiles del usuario principal.
            if ($usuarioAEliminar->perfilEmpleado) {
                $usuarioAEliminar->perfilEmpleado->forceDelete();
            }
            if ($usuarioAEliminar->perfilUsuario) {
                $usuarioAEliminar->perfilUsuario->forceDelete();
            }

            // PASO 4: Eliminar el registro de la tabla 'users'.
            $usuarioAEliminar->forceDelete();

            DB::commit();
            // ✅ Despacha el evento al enviar a la papelera

            return redirect()->back()->with('success', 'Usuario y todos sus datos han sido eliminados permanentemente.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Registra el error real para que puedas depurarlo si vuelve a ocurrir
            \Log::error('Error al eliminar permanentemente al usuario ID ' . $id . ': ' . $e->getMessage());

            return back()->with('error', 'Ocurrió un error inesperado al eliminar el usuario. No se realizaron cambios.');
        }
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     * Puede ser un nuevo propietario con su establecimiento o un empleado de uno existente.
     */
    public function store(Request $request)
    {
        // --- 1. VALIDACIÓN ---
        $request->validate([
            'tipo_usuario' => 'required|in:propietario,empleado',
            'primer_nombre' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'correo' => 'required|email|unique:perfil_usuario,correo',
            'numero_documento' => 'required|string|unique:usuarios,numero_documento',
            'password' => 'required|string|min:4',

        ]);

        try {
            DB::beginTransaction();

            // --- 2. CREACIÓN DEL USUARIO Y PERFIL BÁSICO (COMÚN PARA AMBOS) ---
            $usuario = User::create([
                'numero_documento' => $request->numero_documento,
                'password' => Hash::make($request->password),
                'estado_id' => 1,
            ]);

            PerfilUsuario::create([
                'usuario_id' => $usuario->id,
                'indicativo_id' => 1,
                'primer_nombre' => $request->primer_nombre,
                'primer_apellido' => $request->primer_apellido,
                'correo' => $request->correo,
                'telefono' => $request->telefono ?? '0000000000',
                'rol_id' => ($request->tipo_usuario === 'propietario') ? 1 : 3, // 1: Propietario, 3: Empleado
                'estado_id' => 1,
            ]);

            // --- 3. LÓGICA DIFERENCIADA ---
            if ($request->tipo_usuario === 'propietario') {
                // --- LÓGICA PARA CREAR UN NUEVO PROPIETARIO Y SU ESTABLECIMIENTO ---
                $establecimiento = establecimientos::create([
                    'propietario_id' => $usuario->id,
                    'nombre_establecimiento' => 'Tienda de ' . $request->primer_nombre,
                    'estado_id' => 1,
                    'aplicacion_web_id' => 1, // Asumimos un valor por defecto
                ]);

                $token = TokensAcceso::create([
                    'establecimiento_id' => $establecimiento->id,
                    'token_activacion' => Str::uuid(),
                    'estado_id' => 2, // Inactivo
                ]);

                $establecimiento->update(['token_id' => $token->id]);

                FacturacionMembresias::create([
                    'cliente_id' => $usuario->id,
                    'establecimiento_id' => $establecimiento->id,
                    'aplicacion_web_id' => 1,
                    'monto_total' => 50000,
                    'dias_restantes' => 5,
                    'estado_id' => 16, // Pendiente
                    'medio_pago_id' => 8,
                ]);

                // También se crea un perfil de empleado para el propietario
                PerfilEmpleado::create([
                    'usuario_id' => $usuario->id,
                    'establecimiento_id' => $establecimiento->id,
                    'rol_id' => 1,
                    'cargo' => 'Propietario',
                    'estado_id' => 1,
                ]);

            } else {
                // --- LÓGICA PARA CREAR UN EMPLEADO EN UN ESTABLECIMIENTO EXISTENTE ---
                PerfilEmpleado::create([
                    'usuario_id' => $usuario->id,
                    'establecimiento_id' => $request->establecimiento_id,
                    'rol_id' => 3, // Rol de Empleado
                    'cargo' => $request->cargo,
                    'estado_id' => 1,
                ]);
            }

            DB::commit();
            broadcast(new UserListUpdated())->toOthers();


            return redirect()->back()->with('success', 'Usuario creado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al crear el usuario.');
        }
    }

    public function bulkDestroy(Request $request)
    {
        // 1. Validar que recibimos un array de IDs
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:usuarios,id', // Valida que cada ID exista en la tabla de usuarios
        ]);

        // 2. Autorización
        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            abort(403, 'No tienes permisos para esta acción.');
        }

        $idsAEliminar = $request->input('ids');
        $authId = Auth::id();

        // 3. Filtrar para que el usuario no pueda eliminarse a sí mismo
        $filteredIds = collect($idsAEliminar)->reject(function ($id) use ($authId) {
            return $id == $authId;
        });

        if ($filteredIds->isNotEmpty()) {
            // 4. Eliminar en lote
            User::whereIn('id', $filteredIds)->delete();

            // 5. Despachar el evento para la actualización en tiempo real
            broadcast(new UserListUpdated())->toOthers();
        }

        return redirect()->back()->with('success', 'Usuarios seleccionados enviados a la papelera.');
    }
}
