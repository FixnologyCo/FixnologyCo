<?php

namespace Core\Http\Controllers;

use App\Models\User;
use App\Events\UserListUpdated;
use App\Http\Controllers\Controller;
use Core\Models\AplicacionesWeb;
use Core\Models\Estados;
use Core\Models\Indicativos;
use Core\Models\Membresias;
use Core\Models\PerfilEmpleado;
use Core\Models\PerfilUsuario;
use Core\Models\FacturacionMembresias;
use Core\Models\Establecimientos;
use Core\Models\Roles;
use Core\Models\TipoDocumento;
use Core\Models\TokensAcceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Exception;

class UsuariosFixController extends Controller
{
    use AuthorizesRequests;


    public function show($aplicacion, $rol, Request $request): mixed
    {
        $usuarioAutenticado = Auth::user()->load(['perfilUsuario.rol', 'establecimientoAsignado.aplicacionWeb.estilo']);

        if (!in_array($usuarioAutenticado->perfilUsuario->rol->id, [4])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login.auth')->with('error', 'No tienes permisos para acceder, lo siento :c');
        }


        $misEmpleados = $this->getMisEmpleados($usuarioAutenticado);
        $todosLosUsuarios = $this->getTodosLosUsuarios();
        $usuariosEnPapelera = $this->getUsuariosEnPapelera();
        $establecimientosDisponibles = $this->getEstablecimientosDisponibles();
        $filtrosDisponibles = $this->getDatosParaFiltros();
        $indicativosDisponibles = $this->getIndicativosDisponibles();
        $tipoDocumentoDisponibles = $this->getTipoDocumentoDisponibles();
        $rolesDisponibles = $this->getRolesDisponibles();
        $appsDisponibles = $this->getAppsDisponibles();
        $generosDisponibles = $this->getGenerosDisponibles();
        $estadosDisponibles = $this->getEstadosDisponibles();


        $activeSessionUserIds = DB::table(config('session.table'))
            ->whereNotNull('user_id')->pluck('user_id')->unique();

        $todosLosUsuarios->each(fn($u) => $u->tiene_sesion_activa = $activeSessionUserIds->contains($u->id));
        $misEmpleados->each(fn($u) => $u->tiene_sesion_activa = $activeSessionUserIds->contains($u->id));


        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/GestorUsuarios', [
            'usuario' => $usuarioAutenticado,
            'misEmpleados' => $misEmpleados,
            'todosLosUsuarios' => $todosLosUsuarios,
            'usuariosEnPapelera' => $usuariosEnPapelera,
            'establecimientosDisponibles' => $establecimientosDisponibles,
            'indicativosDisponibles' => $indicativosDisponibles,
            'tipoDocumentoDisponibles' => $tipoDocumentoDisponibles,
            'rolesDisponibles' => $rolesDisponibles,
            'filtrosDisponibles' => $filtrosDisponibles,
            'generosDisponibles' => $generosDisponibles,
            'appsDisponibles' => $appsDisponibles,
            'estadosDisponibles' => $estadosDisponibles
        ]);
    }

    private function getRelationsToLoad(): array
    {
        return [
            'perfilUsuario' => fn($q) => $q->with(['indicativo', 'tipoDocumento', 'estado', 'rol']),
            'perfilEmpleado' => fn($q) => $q->with(['estado', 'medioPago']),
            'establecimientoAsignado' => function ($query) {
                $query->with([
                    'aplicacionWeb' => function ($subQuery) {
                        $subQuery->with(['estilo', 'estado', 'membresia.estado']);
                    },
                    'facturas' => function ($subQuery) {
                        $subQuery->with(['estado', 'medioPago']);
                    },
                    'token.estado',
                    'propietario',
                ]);
            },
            'establecimiento' => fn($q) => $q->with(['token.estado', 'propietario', 'facturas.estado', 'facturas.medioPago']),
        ];
    }

    private function getMisEmpleados(User $usuarioAutenticado)
    {
        $idEstablecimiento = $usuarioAutenticado->establecimientoAsignado->id ?? null;

        if (!$idEstablecimiento) {
            return collect(); // Devuelve una colección vacía si no hay establecimiento
        }

        return User::whereHas('perfilEmpleado', fn($q) => $q->where('establecimiento_id', $idEstablecimiento))
            ->where('id', '!=', $usuarioAutenticado->id)
            ->with($this->getRelationsToLoad())
            ->get();
    }

    private function getTodosLosUsuarios()
    {
        return User::with($this->getRelationsToLoad())->get();
    }

    private function getUsuariosEnPapelera()
    {
        return User::onlyTrashed()->with($this->getRelationsToLoad())->get();
    }

    private function getEstablecimientosDisponibles()
    {
        return Establecimientos::all()->map(function ($e) {
            return [
                'id' => $e->id,
                'text' => $e->nombre_establecimiento
            ];
        });
    }

    private function getDatosParaFiltros(): array
    {
        return [
            'estados' => Estados::whereIn('categoria_estado', ['General', 'Pagos'])->distinct()->pluck('tipo_estado'),
            'aplicaciones' => AplicacionesWeb::distinct()->pluck('nombre_app'),
            'membresias' => Membresias::distinct()->pluck('nombre_membresia'),
            'ciudades' => PerfilUsuario::whereNotNull('ciudad_residencia')->distinct()->pluck('ciudad_residencia'),
        ];
    }

    private function getIndicativosDisponibles()
    {
        return Indicativos::orderBy('pais', 'asc')->get()->map(function ($indicativo) {
            return [
                'id' => $indicativo->id,
                'text' => "{$indicativo->pais} ({$indicativo->codigo_pais})",
            ];
        });
    }

    private function getTipoDocumentoDisponibles()
    {
        return TipoDocumento::orderBy('documento_legal', 'asc')->get()->map(function ($tipoDocumento) {
            return [
                'id' => $tipoDocumento->id,
                'text' => $tipoDocumento->documento_legal
            ];
        });
    }

    private function getRolesDisponibles()
    {
        return Roles::orderBy('tipo_rol', 'asc')->get()->map(function ($rol) {
            return [
                'id' => $rol->id,
                'text' => $rol->tipo_rol
            ];
        });
    }

    private function getGenerosDisponibles()
    {
        return [
            ['id' => 'Femenino', 'text' => 'Femenino'],
            ['id' => 'Masculino', 'text' => 'Masculino'],
            ['id' => 'Otro', 'text' => 'Otro'],
        ];
    }

    private function getAppsDisponibles()
    {

        $aplicaciones = AplicacionesWeb::with('membresia')->get();


        $aplicacionesOrdenadas = $aplicaciones->sortBy(function ($app) {

            return $app->membresia->nombre_membresia ?? 'Sin nombre';
        });

        return $aplicacionesOrdenadas->map(function ($app) {

            $nombreMembresia = $app->membresia->nombre_membresia ?? 'Sin Membresía';

            return [
                'id' => $app->id,
                'text' => "{$app->nombre_app} - {$nombreMembresia}"
            ];
        })->values();
    }

    private function getEstadosDisponibles()
    {

        return Estados::orderBy('tipo_estado', 'asc')->get()->map(function ($estados) {
            return [
                'id' => $estados->id,
                'text' => $estados->tipo_estado
            ];
        });
    }

    public function destroy(User $usuarioAEliminar)
    {
        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            return redirect()->with('error', 'No tienes permisos para realizar esta acción, lo siento :c');
        }
        if ($usuarioAEliminar->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuarioAEliminar->delete();

        return redirect()->back()->with('success', 'Usuario enviado a la papelera.');
    }

    public function trash($aplicacion, $rol)
    {
        $usuarioAutenticado = Auth::user()->load(['perfilUsuario.rol']);

        $usuariosEnPapelera = User::onlyTrashed()
            ->with([
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
                        },
                        'facturas' => function ($subQuery) {
                            $subQuery->with(['estado', 'medioPago']);
                        },
                        'token.estado',
                        'propietario',
                    ]);
                },

                'establecimiento' => function ($query) {
                    $query->with([
                        'facturas' => function ($subQuery) {
                            $subQuery->with(['estado', 'medioPago']);
                        }
                    ]);
                }
            ])
            ->get();

        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/Papelera', [
            'usuario' => $usuarioAutenticado,
            'usuariosEnPapelera' => $usuariosEnPapelera,
        ]);
    }

    public function restore($id)
    {
        // Usamos withTrashed() para encontrar el usuario aunque esté en la papelera
        $usuarioARestaurar = User::withTrashed()->findOrFail($id);

        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            return redirect()->with('error', 'No tienes permisos para realizar esta acción, lo siento :c');
        }

        $usuarioARestaurar->restore();

        return redirect()->back()->with('success', 'Usuario restaurado correctamente.');
    }

    public function forceDestroy($id)
    {
        $usuarioAEliminar = User::withTrashed()->findOrFail($id);

        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            return back()->with('error', 'No tienes permisos para esta acción.');
        }
        if ($usuarioAEliminar->id === Auth::id()) {
            return back()->with('error', 'Acción no permitida.');
        }

        try {
            DB::beginTransaction();
            // Llama al método privado que contiene la lógica de borrado
            $this->_forceDeleteUser($usuarioAEliminar);
            DB::commit();
            return redirect()->back()->with('success', 'Usuario eliminado permanentemente.');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar usuario ID ' . $id . ': ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al eliminar el usuario.');
        }
    }

    public function emptyTrash()
    {
        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            return back()->with('error', 'No tienes permisos para esta acción.');
        }

        // 1. Obtener todos los usuarios en la papelera
        $usuariosEnPapelera = User::onlyTrashed()->get();

        if ($usuariosEnPapelera->isEmpty()) {
            return back()->with('info', 'La papelera ya está vacía.');
        }

        // 2. Iterar y eliminar cada uno usando la lógica refactorizada
        try {
            DB::beginTransaction();
            foreach ($usuariosEnPapelera as $usuario) {
                // Se asegura de no eliminar al usuario autenticado si estuviera en la papelera
                if ($usuario->id !== Auth::id()) {
                    $this->_forceDeleteUser($usuario);
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'La papelera ha sido vaciada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error al vaciar la papelera: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al vaciar la papelera.');
        }
    }

    private function _forceDeleteUser(User $usuarioAEliminar)
    {
        // Carga las relaciones necesarias para el borrado en cascada
        $usuarioAEliminar->load(['perfilUsuario', 'perfilEmpleado', 'establecimiento']);

        // Eliminar facturas donde el usuario es el cliente
        FacturacionMembresias::where('cliente_id', $usuarioAEliminar->id)->forceDelete();

        // Si el usuario es propietario de un establecimiento
        if ($establecimiento = $usuarioAEliminar->establecimiento) {
            // Encuentra y elimina a todos los empleados de ese establecimiento
            $empleadosAEliminar = User::whereHas('perfilEmpleado', function ($query) use ($establecimiento) {
                $query->where('establecimiento_id', $establecimiento->id);
            })->where('id', '!=', $usuarioAEliminar->id)->get();

            foreach ($empleadosAEliminar as $empleado) {
                FacturacionMembresias::where('cliente_id', $empleado->id)->forceDelete();
                $empleado->perfilEmpleado()->forceDelete();
                $empleado->perfilUsuario()->forceDelete();
                $empleado->forceDelete();
            }

            // Elimina las dependencias del establecimiento
            $establecimiento->facturas()->forceDelete();
            $establecimiento->token()->forceDelete();
            $establecimiento->forceDelete();
        }

        // Elimina los perfiles del usuario principal
        if ($usuarioAEliminar->perfilEmpleado) {
            $usuarioAEliminar->perfilEmpleado->forceDelete();
        }
        if ($usuarioAEliminar->perfilUsuario) {
            $usuarioAEliminar->perfilUsuario->forceDelete();
        }

        // Finalmente, elimina el usuario
        $usuarioAEliminar->forceDelete();
    }

    public function bulkDestroy(Request $request)
    {

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:usuarios,id',
        ]);

        if (Auth::user()->perfilUsuario->rol_id !== 4) {
            return redirect()->with('error', 'No tienes permisos para realizar esta acción, lo siento :c');
        }

        $idsAEliminar = $request->input('ids');
        $authId = Auth::id();

        $filteredIds = collect($idsAEliminar)->reject(function ($id) use ($authId) {
            return $id == $authId;
        });

        if ($filteredIds->isNotEmpty()) {
            User::whereIn('id', $filteredIds)->delete();
        }

        return redirect()->back()->with('success', 'Usuarios seleccionados enviados a la papelera.');
    }

    public function store(Request $request)
    {

        $request->validate([
            'primer_nombre' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'correo' => 'required|email|unique:perfil_usuario,correo',
            'numero_documento' => 'required|string|unique:usuarios,numero_documento',
            'password' => 'required|string|min:4',

        ]);

        try {
            DB::beginTransaction();

            $usuario = User::create([
                'numero_documento' => $request->numero_documento,
                'password' => Hash::make($request->password),
                'estado_id' => 1,
            ]);

            PerfilUsuario::create([
                'usuario_id' => $usuario->id,
                'indicativo_id' => $request->indicativo_id,
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre ?? '',
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido ?? '',
                'direccion_residencia' => $request->direccion_residencia ?? '',
                'barrio_residencia' => $request->barrio_residencia ?? '',
                'ciudad_residencia' => $request->ciudad_residencia ?? '',
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'rol_id' => $request->rol_id,
                'genero' => $request->genero,
                'estado_id' => 1,
            ]);


            if ($request->rol_id === 1) {
                // --- INICIO DE LA LÓGICA CORRECTA ---

                // 1. Buscamos la APLICACIÓN WEB que el usuario seleccionó en el formulario.
                $aplicacion = AplicacionesWeb::find($request->aplicacion_id);

                // 2. Validamos primero que esa aplicación exista.
                if (!$aplicacion) {
                    DB::rollBack();
                    return back()->with('error', 'Error: La aplicación web seleccionada no es válida.');
                }

                // 3. A partir de la aplicación, buscamos la MEMBRESÍA que tiene asociada.
                //    Usamos el ID de la membresía que está guardado en la tabla de aplicaciones.
                $membresia = Membresias::find($aplicacion->membresia_id);

                // 4. Validamos que la aplicación tenga una membresía correctamente configurada.
                if (!$membresia) {
                    DB::rollBack();
                    return back()->with('error', 'Error: La aplicación seleccionada no tiene un plan de membresía válido asociado.');
                }

                // --- Con $aplicacion y $membresia encontrados, procedemos a crear los registros ---

                $establecimiento = establecimientos::create([
                    'propietario_id' => $usuario->id,
                    'nombre_establecimiento' => 'Tienda de ' . $request->primer_nombre,
                    'estado_id' => 1,
                    // Aquí usamos el ID de la aplicación que vino en el request
                    'aplicacion_web_id' => $request->aplicacion_id,
                    'tipo_establecimiento' => $request->tipo_establecimiento
                ]);

                $token = TokensAcceso::create([
                    'establecimiento_id' => $establecimiento->id,
                    'token_activacion' => Str::uuid(),
                    'estado_id' => 1,
                ]);

                $establecimiento->update(['token_id' => $token->id]);

                FacturacionMembresias::create([
                    'cliente_id' => $usuario->id,
                    'establecimiento_id' => $establecimiento->id,
                    'aplicacion_web_id' => $request->aplicacion_id,
                    'membresia_id' => $membresia->id, // Opcional pero recomendado
                    'monto_total' => $membresia->precio_membresia,      // Dato correcto
                    'dias_restantes' => $membresia->duracion_membresia, // Dato correcto
                    'estado_id' => 18,
                    'medio_pago_id' => 8,
                ]);

                PerfilEmpleado::create([
                    'usuario_id' => $usuario->id,
                    'establecimiento_id' => $establecimiento->id,
                    'rol_id' => $request->rol_id,
                    'cargo' => $request->cargo,
                    'estado_id' => 1,
                ]);

            } else {
                PerfilEmpleado::create([
                    'usuario_id' => $usuario->id,
                    'establecimiento_id' => $request->establecimiento_id,
                    'rol_id' => 2,
                    'cargo' => $request->cargo,
                    'estado_id' => 1,
                ]);
            }

            DB::commit();
            broadcast(new UserListUpdated())->toOthers();
            return redirect()->back()->with('success', 'Usuario creado con éxito.');

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al crear el usuario.');
        }
    }
}
