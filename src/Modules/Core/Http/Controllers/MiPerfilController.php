<?php

namespace Core\Http\Controllers;

use Core\Models\Indicativos;
use Auth;
use Core\Models\PerfilUsuario;
use Core\Models\TipoDocumento;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;


class MiPerfilController extends Controller
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
        $usuario = Auth::user()->load(
            'perfilUsuario',
            'perfilUsuario.indicativo',
            'perfilUsuario.tipoDocumento',
            'perfilUsuario.estado',
            'perfilEmpleado',
            'perfilEmpleado.estado',
            'perfilEmpleado.medioPago',
            'tienda',
            'tienda.token',
            'tienda.token.estado',
            'tienda.aplicacionWeb',
            'tienda.aplicacionWeb.estilo',
            'tienda.aplicacionWeb.estado',
            'tienda.aplicacionWeb.membresia',
            'tienda.aplicacionWeb.membresia.estado',
            'tienda.facturas',
            'tienda.facturas.estado',
            'tienda.facturas.medioPago',
            'tienda.propietario'

        );
        $tipoDeRol = $usuario->rol->tipo_rol;
        $aplicacionWeb = $usuario->tienda[0]->aplicacionWeb->nombre_app ?? null;



        if (!in_array($usuario->rol->id, [1, 2, 4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/MiPerfil/MiPerfil', [
            'usuario' => $usuario,
            'rol' => $tipoDeRol
        ]);
    }

    public function formUpdate($aplicacion, $rol, Request $request)
    {
        $usuario = Auth::user()->load(

            'perfilUsuario.indicativo',
            'perfilUsuario.tipoDocumento',
                'perfilUsuario.estado',
            'perfilEmpleado',
            'perfilEmpleado.estado',
            'perfilEmpleado.medioPago',
            'tienda',
            'tienda.token',
            'tienda.token.estado',
            'tienda.aplicacionWeb',
            'tienda.aplicacionWeb.estilo',
            'tienda.aplicacionWeb.estado',
            'tienda.aplicacionWeb.membresia',
            'tienda.aplicacionWeb.membresia.estado',
            'tienda.facturas',
            'tienda.facturas.estado',
            'tienda.facturas.medioPago',
            'tienda.propietario'

        );
        $tipoDeRol = $usuario->rol->tipo_rol;


        $indicativos = Indicativos::all();
        $tipoDocumentos = TipoDocumento::all();



        if (!in_array($usuario->rol->id, [1, 2, 4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/MiPerfil/EditarMiPerfil', [
            'usuario' => $usuario,
            'rol' => $tipoDeRol,
            'indicativos' => $indicativos,
            'tipoDocumentos' => $tipoDocumentos
        ]);
    }

    public function actualizarFotoPerfil(Request $request, $aplicacion, $rol)
    {

        // 2. CREA EL VALIDADOR MANUALMENTE
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'image', 'max:3048'], // 2MB
        ], [
            'photo.max' => 'La imagen no debe superar los 3MB.',
        ]);


        if ($validator->fails()) {
            // Si la validación falla, redirige con tu mensaje flash
            return redirect()->route('aplicacion.miPerfil.editarMiPerfil', [
                'aplicacion' => $aplicacion,
                'rol' => $rol,
            ])->with('error', 'La imagen es muy pesada y no debe superar los 3MB.');
        }

        $user = $request->user();
        $perfil = $user->perfilUsuario;

        $directory = 'fotosUsuarios/' . $user->id;

        Storage::disk('public')->deleteDirectory($directory);

        $path = $request->file('photo')->store($directory, 'public');

        $perfil->forceFill([
            'ruta_foto' => $path,
        ])->save();

        return Redirect::route('aplicacion.miPerfil.editarMiPerfil', [
            'aplicacion' => $aplicacion,
            'rol' => $rol
        ])->with('success', '¡Foto de perfil actualizada con éxito!.');
    }

    public function actualizarFotoTienda(Request $request, $aplicacion, $rol)
    {
        $validator = Validator::make($request->all(), [
            'photo' => ['required', 'image', 'max:3072'], // 3072 KB = 3MB
        ], [
            'photo.max' => 'La imagen no debe superar los 3MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('aplicacion.miPerfil.editarMiPerfil', [
                'aplicacion' => $aplicacion,
                'rol' => $rol,
            ])->with('error', 'La imagen es muy pesada y no debe superar los 3MB.');
        }

        $user = $request->user();


        $establecimiento = $user->tienda()->first();


        if (!$establecimiento) {
            return redirect()->route('aplicacion.miPerfil.editarMiPerfil', [
                'aplicacion' => $aplicacion,
                'rol' => $rol,
            ])->with('error', 'No se encontró ningún establecimiento asociado a tu cuenta.');
        }


        $directory = 'fotosEstablecimientos/' . $establecimiento->id;

        Storage::disk('public')->deleteDirectory($directory);

        $path = $request->file('photo')->store($directory, 'public');


        $establecimiento->forceFill([
            'ruta_foto_establecimiento' => $path,
        ])->save();

        return Redirect::route('aplicacion.miPerfil.editarMiPerfil', [
            'aplicacion' => $aplicacion,
            'rol' => $rol
        ])->with('success', '¡Foto del establecimiento actualizada con éxito!');
    }

    // En tu MiPerfilController.php

public function actualizarPerfilUsuario(Request $request, $aplicacion, $rol)
{
    // 1. Obtén el usuario y sus relaciones
    $user = $request->user();
    $perfilUsuario = $user->perfilUsuario;
    $establecimiento = $user->tienda()->first();

    // 2. Valida TODOS los campos que vienen del formulario
    $validatedData = $request->validate([
        'primer_nombre' => 'required|string|max:50',
        'segundo_nombre' => 'nullable|string|max:50',
        'primer_apellido' => 'required|string|max:50',
        'segundo_apellido' => 'nullable|string|max:50',
        'indicativo_id' => 'required|exists:indicativos,id',
        'telefono' => 'required|numeric|digits_between:7,15',
        'tipo_documento_id' => 'required|exists:tipo_documentos,id',
        'numero_documento' => 'required|string|max:20|unique:usuarios,numero_documento,' . $user->id,
        'direccion' => 'nullable|string|max:255',
        'ciudad' => 'nullable|string|max:100',
        'barrio' => 'nullable|string|max:100',
        'email' => 'required|email|max:60',
        
        // --- Validación para el establecimiento ---
        'nombre_tienda' => 'required|string|max:100',
        'tipo_tienda' => 'nullable|string|max:100',
        'telefono_establecimiento' => 'required|numeric|digits_between:7,15',
        'ciudad_establecimiento' => 'nullable|string|max:100',
        'barrio_establecimiento' => 'nullable|string|max:100',
        'email_establecimiento' => 'required|email|max:60',
        'direccion_establecimiento' => 'required|string|max:60',
        // ... agrega las demás validaciones para la tienda
    ]);

    // 3. Actualiza la tabla `perfil_usuario`
    if ($perfilUsuario) {
        $perfilUsuario->update([
            'primer_nombre' => $validatedData['primer_nombre'],
            'segundo_nombre' => $validatedData['segundo_nombre'],
            'primer_apellido' => $validatedData['primer_apellido'],
            'segundo_apellido' => $validatedData['segundo_apellido'],
            'indicativo_id' => $validatedData['indicativo_id'],
            'telefono' => $validatedData['telefono'],
            'tipo_documento_id' => $validatedData['tipo_documento_id'],
            'direccion_residencia' => $validatedData['direccion'],
            'ciudad_residencia' => $validatedData['ciudad'],
            'barrio_residencia' => $validatedData['barrio'],
            'email' => $validatedData['email'],
        ]);
    }

    // 4. Actualiza la tabla `users` (si hay campos que le pertenecen)
    $user->update([
        'numero_documento' => $validatedData['numero_documento'],
    ]);

    // 5. Actualiza la tabla `establecimientos`
    if ($establecimiento) {
        $establecimiento->update([
            'nombre_establecimiento' => $validatedData['nombre_tienda'],
            'tipo_establecimiento' => $validatedData['tipo_tienda'],
            'telefono_establecimiento' => $validatedData['telefono_establecimiento'],
            'email_establecimiento' => $validatedData['email_establecimiento'],
            'direccion_establecimiento' => $validatedData['direccion_establecimiento'],
            'ciudad_establecimiento' => $validatedData['ciudad_establecimiento'],
            'barrio_establecimiento' => $validatedData['barrio_establecimiento'],
            
        ]);
    }

    // 6. Redirige con un mensaje de éxito
    return Redirect::back()->with('success', 'Tu perfil ha sido actualizado correctamente.');
}

    use AuthorizesRequests;

}
