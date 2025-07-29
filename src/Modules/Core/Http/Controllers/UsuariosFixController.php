<?php

namespace Core\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UsuariosFixController extends Controller
{
    use AuthorizesRequests;

    public function show($aplicacion, $rol, Request $request)
    {
        // 1. Cargamos el usuario y las relaciones que SÍ existen y necesitamos.
        $usuarioAutenticado = Auth::user()->load([
            'perfilUsuario.rol', // Carga el rol a través del perfil
            'establecimientos.aplicacionWeb' // Carga los establecimientos y su app web
        ]);

        // 2. Verificación de permisos de forma segura.
        // Usamos el método getRol() que definimos en el modelo User.
        if (!in_array($usuarioAutenticado->getRol()->id ?? null, [4])) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $idEstablecimiento = 1;

        // 3. Obtenemos los usuarios del establecimiento.
        // La consulta ahora es simple y directa gracias a los modelos corregidos.
        $usuariosDelEstablecimiento = User::whereHas('perfilEmpleado', function ($query) use ($idEstablecimiento) {
            $query->where('establecimiento_id', $idEstablecimiento);
        })
        ->with(['perfilUsuario.rol', 'perfilEmpleado'])
        ->get();

        // 4. HERRAMIENTA DE DEPURACIÓN:
        // Descomenta la siguiente línea para detener el script y ver si los datos son correctos.
        // Si ves la lista de usuarios aquí, ¡el backend funciona! El problema estaría en Vue.
        // dd($usuariosDelEstablecimiento->toArray());

        // 5. Renderizamos la vista con los datos correctos.
        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Usuarios/GestorUsuarios', [
            
            // CORRECCIÓN CLAVE: Usamos la relación 'establecimientos' (plural) y obtenemos el primero.
            'aplicacion' => $usuarioAutenticado->establecimientos->first()->aplicacionWeb->nombre_app ?? null,
            
            'rol' => $usuarioAutenticado->getRol()->tipo_rol ?? null,
            'usuario' => $usuarioAutenticado,
            'usuarios' => $usuariosDelEstablecimiento,
        ]);
    }
}
