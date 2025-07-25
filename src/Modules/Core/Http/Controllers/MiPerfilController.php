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

    public function updateProfilePhoto(Request $request, $aplicacion, $rol)
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

    use AuthorizesRequests;

}
