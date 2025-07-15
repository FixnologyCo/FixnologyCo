<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Asegúrate de que el namespace de tu modelo User sea este
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function show()
    {
        return Inertia::render('Core/Auth/Auth');
    }

    /**
     * Procesa el intento de login.
     */
    public function login(Request $request)
    {
        // 1. Validación de los datos de entrada
        $credenciales = $request->validate([
            'numero_documento' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Intento de Autenticación con el método de Laravel
        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();

            // Cargamos el usuario con su perfil y el rol de ese perfil. ¡Más eficiente!
            $usuario = Auth::user()->load('perfilUsuario.rol');

            // --- VALIDACIONES GENERALES ---
            if ($usuario->estado_id !== 1) { // 1 = Activo
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'Tu cuenta está inactiva o suspendida.']);
            }

            if (!$usuario->perfilUsuario || !$usuario->perfilUsuario->rol) {
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'Tu usuario no tiene un perfil o rol asignado.']);
            }

            // --- LÓGICA BASADA EN ROLES ---
            $rol = $usuario->perfilUsuario->rol;
            $nombreAplicacion = null;

            // Si es un rol que trabaja en un establecimiento (IDs 1, 2, 4 son Admin, Empleado, SuperAdmin)
            if (in_array($rol->id, [1, 2, 4])) {
                
                // Buscamos su perfil de empleado y cargamos las relaciones necesarias
                $perfilEmpleado = $usuario->perfilEmpleado()->with('establecimiento.aplicacionWeb')->first();

                if (!$perfilEmpleado || !$perfilEmpleado->establecimiento) {
                    Auth::logout();
                    return back()->withErrors(['numero_documento' => 'No tienes un rol válido asignado en ningún establecimiento.']);
                }

                $establecimiento = $perfilEmpleado->establecimiento;

                if ($establecimiento->estado_id !== 1) {
                    Auth::logout();
                    return back()->withErrors(['numero_documento' => 'El establecimiento al que perteneces está inactivo.']);
                }

                if ($establecimiento->token?->estado_id !== 1) {
                    Auth::logout();
                    return back()->withErrors(['numero_documento' => 'El token de tu establecimiento está inactivo.']);
                }

                $ultimaFactura = $establecimiento->facturas()->latest('fecha_pago')->first();
                if ($ultimaFactura && $ultimaFactura->estado_id === 16) { // Suponiendo que 16 es 'Pendiente'
                    Auth::logout();
                    return back()->with('error', 'Tu establecimiento tiene un pago pendiente. Por favor, ponte al día.');
                }
                
                // Obtenemos el nombre de la aplicación desde el establecimiento
                $nombreAplicacion = $establecimiento->aplicacionWeb?->nombre_app;

            } elseif ($rol->id === 3) { // 3 = Cliente
                
                // Para un cliente, no validamos establecimiento.
                // Le asignamos un nombre de aplicación por defecto.
                $nombreAplicacion = 'ClienteApp'; // Puedes cambiar esto o hacerlo más dinámico

            } else {
                // Si el rol no es reconocido, cerramos sesión
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'Tu rol no tiene un destino definido.']);
            }
            
            // Si después de la lógica, no tenemos un nombre de aplicación, es un error
            if (!$nombreAplicacion) {
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'No se pudo determinar la aplicación para tu rol.']);
            }

            // --- REDIRECCIÓN FINAL ---
            $nombreCompletoUsuario = $usuario->perfilUsuario->primer_nombre . ' ' . $usuario->perfilUsuario->primer_apellido;

            return redirect()->route('aplicacion.dashboard', parameters: [
                'aplicacion' => $nombreAplicacion,
                'rol' => $rol->tipo_rol,
            ])->with('success', '¡Bienvenido nuevamente, '. trim($nombreCompletoUsuario) .'!');
        }

        return back()->withErrors([
            'numero_documento' => 'Las credenciales proporcionadas no son correctas.',
        ])->onlyInput('numero_documento');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Has cerrado sesión correctamente.');
    }
}
