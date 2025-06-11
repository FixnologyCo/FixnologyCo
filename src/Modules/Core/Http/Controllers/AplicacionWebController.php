<?php

namespace Core\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Core\Models\PlanAplicacion;
use Core\Models\AplicacionWeb;
use Core\Models\ClienteFixgi;
use Core\Models\Estados;
use Core\Models\Membresia;
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

            $apps = AplicacionWeb::All();

            $aplicaciones = AplicacionWeb::with([
                'estado',
                'plan',
                'membresia',
                'tiendas',
                'tiendas.clientes',
                'tiendas.clientes.estado',
                'tiendas.membresia',
            ])->get();

            foreach ($aplicaciones as $app) {
                $usuariosTotales = 0;
                $detallesUsuarios = [];

                foreach ($app->tiendas as $tienda) {
                    foreach ($tienda->clientes as $users) {
                        $usuariosTotales++;
                        $detallesUsuarios[] = [
                            'nombres' => $users->nombres_ct . ' ' . $users->apellidos_ct,
                            'foto' => $users->foto_binaria
                                ? 'data:image/jpeg;base64,' . $users->foto_binaria
                                : null,
                            'tienda' => $tienda->nombre_tienda ?? 'Desconocido',
                        ];
                    }

                }


                $app->usuarios_en_tiendas = $usuariosTotales;
                $app->detalle_usuarios = $detallesUsuarios;
            }

            $estados = Estados::all();
            $plan_aplicaciones = PlanAplicacion::all();
            $membresias = Membresia::all();


            $fotoBase64 = $user->foto_binaria
                ? 'data:image/jpeg;base64,' . $user->foto_binaria
                : null;

            return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/MisApps/MisApps', [
                'auth' => ['user' => $user],
                'aplicacion' => $aplicacion,
                'rol' => $rol,
                'foto_base64' => $fotoBase64,
                'apps' => $aplicaciones,
                'estados' => $estados,
                'plan_aplicaciones' => $plan_aplicaciones,
                'membresias' => $membresias
            ]);

        }

        abort(404);
    }

    public function createApp(Request $request)
    {

        // ✅ Validación de datos
        $request->validate([
            'nombre_app' => 'required|string|max:30|unique:aplicaciones_web,nombre_app',
            'descripcion_app' => 'required|string|max:100',
            'id_estado' => 'required|integer|exists:estados,id',
            'id_plan_aplicacion' => 'required|integer|exists:planes_Aplicaciones,id',
            'id_membresia' => 'required|integer|exists:membresias,id',
            'color_fondo' => 'nullable|string|max:20',
            'icono_app' => 'nullable|string|max:50',

        ], [
            'nombre_app.required' => 'El nombre de app es requerida.',
            'descripcion_app.required' => 'La descripción es requerida',
            'id_estado.required' => 'El estados de app es requerida.',
            'id_plan_aplicacion.required' => 'El plan de la app es requerida.',
            'id_membresia.required' => 'La membresía de la app es requerida.',
            'nombre_app.unique' => 'Oh, oh, este nombre ya se usó.',
            'id_estado.exists' => 'El estado seleccionado no es válido.',
            'id_plan_aplicacion.exists' => 'El plan seleccionado no es válido.',
            'id_membresia.exists' => 'La membresía seleccionada no es válida.',

            'nombre_app.max' => 'El nombre no debe superar los 30 carácteres',
            'descripcion_app.max' => 'La descripción no debe superar los 100 carácteres',
        ]);

        $app = AplicacionWeb::create([
            'nombre_app' => $request->nombre_app,
            'descripcion' => $request->descripcion_app,
            'id_estado' => $request->id_estado,
            'id_plan_aplicacion' => $request->id_plan_aplicacion,
            'id_membresia' => $request->id_membresia,
            'color_fondo' => $request->color_fondo,
            'icono_app' => $request->icono_app,
        ]);

        $nombreAplicacion = $cliente->tienda->aplicacion->nombre_app ?? null;
        $rol = $cliente->rol->tipo_rol ?? null;

        return redirect()->back()->with('success', 'Creación éxitosa, ¡A vender!');
    }

    public function cambiarEstado(Request $request, $id)
    {
        $app = AplicacionWeb::findOrFail($id);

        $estado = Estados::where('tipo_estado', strtolower($request->estado))->first();

        if (!$estado) {
            return response()->json(['error' => 'Estado no válido.'], 422);
        }

        $app->id_estado = $estado->id;
        $app->save();

        return redirect()->back()->with('success', 'Cambio de estado éxitoso.');
    }

    public function updateApp(Request $request, $id)
    {
        $request->validate([
            'nombre_app' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'id_estado' => 'required|integer|exists:estados,id',
            'id_plan_aplicacion' => 'required|integer|exists:planes_aplicaciones,id',
            'id_membresia' => 'required|integer|exists:membresias,id',
            'color_fondo' => 'nullable|string|max:100',
            'icono_app' => 'nullable|string|max:100',
        ]);

        $app = AplicacionWeb::findOrFail($id);
        $app->update([
            'nombre_app' => $request->nombre_app,
            'descripcion' => $request->descripcion_app,
            'id_estado' => $request->id_estado,
            'id_plan_aplicacion' => $request->id_plan_aplicacion,
            'id_membresia' => $request->id_membresia,
            'color_fondo' => $request->color_fondo,
            'icono_app' => $request->icono_app,
        ]);

        return redirect()->back()->with('success', 'Aplicación actualizada correctamente.');
    }

    use AuthorizesRequests;

}


