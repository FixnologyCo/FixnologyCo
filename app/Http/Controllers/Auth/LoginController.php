<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // ✅ Es una buena práctica usar el modelo estándar de Laravel
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
        ], [
            'numero_documento.required' => 'El número de documento es requerido.',
            'password.required' => 'La contraseña es requerida.',
        ]);

        // 2. Intento de Autenticación Automático con el método de Laravel
        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            $usuario = Auth::user(); // Obtenemos la instancia del usuario autenticado

            // 3. Validaciones de Negocio sobre el usuario y su contexto

            // ✅ Valida que la cuenta del usuario esté activa
            if ($usuario->estado_id !== 1) { // Suponiendo que 1 = 'Activo'
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'Tu cuenta está inactiva o suspendida.']);
            }

            // Obtenemos el primer perfil de empleado para saber a qué establecimiento pertenece
            $perfilEmpleado = $usuario->perfilesEmpleado()->with([
                'establecimiento.token',
                'establecimiento.aplicacionWeb.membresia',
                'establecimiento.facturas'
            ])->first();

            // Si es un rol que requiere estar en un establecimiento (Admin, Empleado, etc.)
            // y no tiene un perfil, no puede continuar.
            // (Ajusta los IDs de los roles según tu tabla `roles`)
            if (in_array($usuario->perfilUsuario->rol_id, [1, 2, 4]) && !$perfilEmpleado) {
                Auth::logout();
                return back()->withErrors(['numero_documento' => 'No tienes un rol asignado en ningún establecimiento.']);
            }

            // Si tiene un perfil, validamos el establecimiento
            if ($perfilEmpleado) {
                $establecimiento = $perfilEmpleado->establecimiento;

                // Valida que el establecimiento esté activo
                if ($establecimiento->estado_id !== 1) {
                    Auth::logout();
                    return back()->withErrors(['numero_documento' => 'El establecimiento al que perteneces está inactivo.']);
                }

                // Valida que el token del establecimiento esté activo
                if ($establecimiento->token?->estado_id !== 1) {
                    Auth::logout();
                    return back()->withErrors(['numero_documento' => 'El token de tu establecimiento está inactivo o no existe.']);
                }

                // Valida el estado del último pago de la membresía del establecimiento
                $ultimaFactura = $establecimiento->facturas()->latest('fecha_pago')->first();

                // Si existe una factura y su estado es 8 (Pendiente, según tu descripción)
                if ($ultimaFactura && $ultimaFactura->estado_id === 16) {
                    Auth::logout();
                    return back()->with('error', 'Tu establecimiento tiene un pago pendiente. Por favor, ponte al día.');
                }
            }

            // 4. Redirección Exitosa
            return redirect()->route('aplicacion.dashboard', [

                

            ])->with('success', 'Bienvenido por aquí, ' . ($cliente->nombres_ct ?? 'Usuario'));
        }

        // Si Auth::attempt() falla, las credenciales son incorrectas
        return back()->withErrors([
            'numero_documento' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('numero_documento');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Has cerrado sesión correctamente.');
    }
}