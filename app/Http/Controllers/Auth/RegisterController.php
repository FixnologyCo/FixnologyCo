<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Core\Models\establecimientos;
use Core\Models\FacturacionMembresias;
use Core\Models\PerfilEmpleado;
use Core\Models\PerfilUsuario;
use Core\Models\TokensAcceso;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importante para usar transacciones
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RegisterController extends Controller
{
    // ✅ Mostrar formulario de registro
    public function show()
    {
        return Inertia::render('Core/Auth/Registro');
    }

    // ✅ Lógica de registro mejorada
    public function register(Request $request)
    {
        // --- 1. VALIDACIÓN MEJORADA ---
        $request->validate([
            'primer_nombre' => 'required|string|max:25',
            'primer_apellido' => 'required|string|max:25',
            'numero_documento' => 'required|string|max:15|unique:usuarios,numero_documento',
            'password' => 'required|string|min:4',
        ], [
            'primer_nombre.required' => 'El nombre es requerido.',
            'primer_apellido.required' => 'El apellido es requerido.',
            'numero_documento.required' => 'El número de documento es requerido.',
            'numero_documento.unique' => 'Oh, oh. Este documento ya existe.',
          
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos 4 caracteres.',
        ]);

        // --- 2. TRANSACCIÓN DE BASE DE DATOS ---
        // Esto asegura que si algo falla, todas las operaciones se revierten.
        try {
            DB::beginTransaction();

            // ✅ Crear Usuario
            $usuario = User::create([
                'numero_documento' => $request->numero_documento,
                'password' => Hash::make($request->password),
                'estado_id' => 1, // Por defecto: Activo
            ]);

            // ✅ Crear Perfil de Usuario con valores por defecto
            $perfilUsuario = PerfilUsuario::create([
                'usuario_id' => $usuario->id,
                'primer_nombre' => $request->primer_nombre,
                'primer_apellido' => $request->primer_apellido,
                'telefono' => $request->telefono ?? '0000000000', 
                'indicativo_id' => 1, 
                'rol_id' => 1,
                'estado_id' => 1,
                'ruta_foto' => 'https://placehold.co/400x400/f05235/FFFFFF?text='.$request->primer_nombre,
            ]);

            // ✅ Crear Establecimiento por defecto
            $establecimiento = establecimientos::create([
                'propietario_id' => $usuario->id,
                'estado_id' => 1,
                'aplicacion_web_id' => 1,
                'nombre_establecimiento' => 'Establecimiento de ' . $perfilUsuario->primer_nombre,
                'ruta_foto_establecimiento' => 'https://placehold.co/400x400/f05235/FFFFFF?text=Mi tienda',
            ]);

            // ✅ Crear Perfil de Empleado para el propietario
            PerfilEmpleado::create([
                'usuario_id' => $usuario->id,
                'establecimiento_id' => $establecimiento->id,
                'rol_id' => 1, 
                'cargo' => 'Propietario', 
                'estado_id' => 1,
                'medio_pago_id' => 1,
            ]);

            // ✅ Crear Token de Activación
            $token = TokensAcceso::create([
                'establecimiento_id' => $establecimiento->id,
                'token_activacion' => Str::uuid(),
                'estado_id' => 2,
            ]);

            // ✅ Vincular Token al Establecimiento
            $establecimiento->update(['token_id' => $token->id]);

            // ✅ Crear Factura de Membresía inicial (ej. prueba gratuita)
            FacturacionMembresias::create([
                'cliente_id' => $usuario->id,
                'establecimiento_id' => $establecimiento->id,
                'aplicacion_web_id' => $establecimiento->aplicacion_web_id,
                'monto_total' => 50000,
                'dias_restantes' => 5, 
                'estado_id' => 16, 
                'medio_pago_id' => 8,
            ]);

           
            DB::commit();

            
            return redirect()->route('login.auth')->with('success', '¡Registro exitoso! Revisa tu correo para activar tu cuenta.');

        } catch (Exception $e) {
           
            DB::rollBack();

            
            return back()->with('error', 'Ocurrió un error inesperado durante el registro. Por favor, inténtalo de nuevo.');
        }
    }
}
