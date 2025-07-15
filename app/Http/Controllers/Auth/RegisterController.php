<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Core\Models\Establecimientos;
use Core\Models\FacturacionMembresias;
use Core\Models\PerfilEmpleado;
use Core\Models\PerfilUsuario;
use Core\Models\TokensAcceso;
use Core\Models\TipoDocumento;
use Core\Models\AplicacionesWeb;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    // ✅ Mostrar formulario de registro
    public function show()
    {


        return Inertia::render('Core/Auth/Registro', [

        ]);
    }

    // ✅ Lógica de registro
    public function register(Request $request)
    {

        // ✅ Validación de datos
        $request->validate([
            'primer_nombre' => 'required|string|max:25',
            'primer_apellido' => 'required|string|max:25',
            'numero_documento' => 'required|string|max:15|unique:usuarios,numero_documento',
            'password' => 'required|string|min:4',


        ], [
            'primer_nombre.required' => 'El nombre es requeridos.',
            'primer_apellido.required' => 'El apellido es requeridos.',
            'numero_documento.required' => 'El número de documento es requerido.',
            'numero_documento.unique' => 'Oh, oh. Este documento ya existe',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe ser minimo de 4 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',

        ]);

        // ✅ Crear cliente
        $usuario = User::create([
            'numero_documento' => $request->numero_documento,
            'password' => Hash::make($request->password),
            'estado_id' => 1,

        ]);

        $perfil_usuario = PerfilUsuario::create([
            'primer_nombre' => $request->primer_nombre,
            'primer_apellido' => $request->primer_apellido,
            'usuario_id' => $usuario->id
        ]);

        $establecimiento = Establecimientos::create([
            'estado_id' => 1,
            'aplicacion_web_id' => 1,
            'propietario_id' => $usuario->id,
            'nombre_establecimiento' => 'Tienda de ' . $perfil_usuario->primer_nombre,
            'ruta_foto_establecimiento' => 'https://imgmedia.larepublica.pe/640x959/larepublica/original/2025/01/07/677d743f33031862432cca4d.webp'
        ]);

        $perfil_empleado = PerfilEmpleado::create([
            'estado_id' => 1,
            'usuario_id' => $usuario->id,
            'establecimiento_id' => $establecimiento->id,
            'rol_id' => 1,
            'medio_pago_id' => 1
        ]);


        $usuario->update(['id_propietario' => $establecimiento->id]);

        $token = TokensAcceso::create([
            'estado_id' => 2,
            'token_activacion' => Str::uuid(),
            'establecimiento_id' => $establecimiento->id,
        ]);

        // ✅ Actualizar el id_token en la tienda para que quede vinculado
        $establecimiento->update(['token_id' => $token->id]);

        FacturacionMembresias::create([
            'cliente_id' => $usuario->id,
            'establecimiento_id' => $establecimiento->id,
            'aplicacion_web_id' => $establecimiento->aplicacion_web_id,
            'estado_id' => 16,
            'medio_pago_id' => 8,
            'monto_total' => 50000,
            'dias_restantes' => 5,
        ]);

        // ✅ Guarda el mensaje en la sesión y redirige a login con Inertia
        return redirect()->route('login.auth')->with('success', 'Registro exitoso, Activa el token y ¡empecemos!');

    }

 
}
