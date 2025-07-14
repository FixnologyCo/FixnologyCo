<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Asegúrate de usar el modelo correcto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function show()
    {
        return Inertia::render('Core/Auth/Auth');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'numero_documento' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();

            // Cargamos el usuario con su perfil personal y el rol de ese perfil. ¡Más eficiente!
            $usuario = Auth::user()->load('perfilUsuario.rol');

            // --- VALIDACIONES GENERALES ---
            if ($usuario->estado_id !== 1) { // 1 = Activo
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'Tu cuenta está inactiva o suspendida.']);
            }

            // Variables que llenaremos según el rol
            $rol = $usuario->perfilUsuario->rol->tipo_rol;
            $nombreAplicacion = null;

            // --- LÓGICA BASADA EN ROLES ---
            
            // Si es un rol que trabaja en un establecimiento (IDs 1, 2, 4 son Admin, Empleado, SuperAdmin)
            if (in_array($usuario->perfilUsuario->rol_id, [1, 2, 4])) {
                
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

            } elseif ($usuario->perfilUsuario->rol_id === 3) { // 3 = Cliente
    
                // Para un cliente, quizás no necesitemos validar un establecimiento.
                // Tal vez solo usan una aplicación general.
                // Aquí podrías poner validaciones específicas para clientes si las tuvieras.
                
                // Asignamos un nombre de aplicación por defecto o el que corresponda a clientes.
                $nombreAplicacion = 'ClienteApp'; // Puedes cambiar esto

            } else {
                // Si el rol no es reconocido, cerramos sesión
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'Tu rol no tiene un destino definido.']);
            }
            
            // Si no obtuvimos un nombre de aplicación, es un error de configuración
            if (!$nombreAplicacion) {
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'No se pudo determinar la aplicación para tu rol.']);
            }


            // --- REDIRECCIÓN FINAL ---
            
            $primerNombreUsuario = $usuario->perfilUsuario->primer_nombre;
            $primerApellidoUsuario = $usuario->perfilUsuario->primer_apellido;
            $nombreCompletoUsuario = $primerNombreUsuario . ' ' . $primerApellidoUsuario;

            // Pasamos las variables a la ruta del dashboard
            return redirect()->route('aplicacion.dashboard', [
                'aplicacionWeb' => $nombreAplicacion,
                'rol' => $rol,
            ])->with('success', '¡Bienvenido nuevamente, '. $nombreCompletoUsuario. '!');
        }

       

        return redirect()->route('login.auth')->with('error', 'Las credenciales proporcionadas no son correctas.');

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Has cerrado sesión correctamente.');
    }
}