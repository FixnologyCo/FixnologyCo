<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Core\Models\AplicacionesWeb;
use Core\Models\establecimientos;
use Core\Models\FacturacionMembresias;
use Core\Models\Membresias;
use Core\Models\PerfilEmpleado;
use Core\Models\PerfilUsuario;
use Core\Models\TokensAcceso;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Events\UserListUpdated;


class RegisterController extends Controller
{
    // ✅ Mostrar formulario de registro
    public function show()
    {

        $appsDisponibles = $this->getAppsDisponibles();

        return Inertia::render('Core/Auth/Registro', ['appsDisponibles' => $appsDisponibles,]);
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
            'aplicacion_web_id' => 'required|exists:aplicaciones_web,id',
        ], [
            'primer_nombre.required' => 'El nombre es requerido.',
            'primer_apellido.required' => 'El apellido es requerido.',
            'numero_documento.required' => 'El número de documento es requerido.',
            'numero_documento.unique' => 'Oh, oh. Este documento ya existe.',

            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos 4 caracteres.',
            'aplicacion_web_id.required' => 'Debes seleccionar una app.',
        ]);

        try {
            DB::beginTransaction();

            // ✅ Crear Usuario
            $usuario = User::create([
                'numero_documento' => $request->numero_documento,
                'password' => Hash::make($request->password),
                'estado_id' => 1,
            ]);

            $perfilUsuario = PerfilUsuario::create([
                'estado_id' => 1,
                'usuario_id' => $usuario->id,
                'rol_id' => 1,
                'indicativo_id' => 1,
                'tipo_documento_id' => 1,
                'ruta_foto' => 'https://placehold.co/400x400/f05235/FFFFFF?text=' . $request->primer_nombre,
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre ?? '',
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido ?? '',
                'telefono' => 'Por definir',
                'correo' => 'Cambiar@urgente.mente',
                'genero' => 'Otro',
                'direccion_residencia' => 'Por definir',
                'ciudad_residencia' => 'Por definir',
                'barrio_residencia' => 'Por definir',
            ]);

            // ✅ Crear Establecimiento por defecto
            $establecimiento = establecimientos::create([
                'estado_id' => 1,
                'aplicacion_web_id' => $request->aplicacion_web_id ?? 0,
                'propietario_id' => $usuario->id,
                'ruta_foto_establecimiento' => 'https://placehold.co/400x400/f05235/FFFFFF?text=tienda id' . $perfilUsuario->usuario_id,
                'tipo_establecimiento' => 'Por definir',
                'nombre_establecimiento' => 'Establecimiento de ' . $perfilUsuario->primer_nombre,
                'email_establecimiento' => 'establecimiento-' . $usuario->id . '@example.com',
                'telefono_establecimiento' => '0000000000',
                'direccion_establecimiento' => 'Por definir',
                'barrio_establecimiento' => 'Por definir',
                'ciudad_establecimiento' => 'Por definir',
            ]);

            // ✅ Crear Perfil de Empleado para el propietario
            PerfilEmpleado::create([
                'estado_id' => 1,
                'usuario_id' => $usuario->id,
                'establecimiento_id' => $establecimiento->id,
                'rol_id' => $perfilUsuario->rol_id,
                'medio_pago_id' => 1,
                'cargo' => 'Propietario',
                'salario_base' => 0,
                'cuenta_ahorros' => $perfilUsuario->telefono,
                'banco' => 'Por definir',
                'horarios' => 'Lunes a Viernes 8:00 AM - 5:00 PM',
                'tipo_contrato' => 'Registro desde index',
                'documentos_zip' => 'NA',
                'fecha_ingreso' => $usuario->created_at,
            ]);

            // ✅ Crear Token de Activación
            $token = TokensAcceso::create([
                'estado_id' => 2,
                'establecimiento_id' => $establecimiento->id,
                'token_activacion' => Str::uuid(),
            ]);

            // ✅ Vincular Token al Establecimiento
            $establecimiento->update(['token_id' => $token->id]);
            $aplicacion = AplicacionesWeb::find($request->aplicacion_web_id);


            if (!$aplicacion) {
                DB::rollBack();
                return back()->with('error', 'La aplicación web seleccionada no es válida.');
            }

            $membresia = Membresias::find($aplicacion->membresia_id);


            if (!$membresia) {
                DB::rollBack();
                return back()->with('error', 'La aplicación seleccionada no tiene un plan de membresía válido asociado.');
            }

            FacturacionMembresias::create([
                'cliente_id' => $usuario->id,
                'establecimiento_id' => $establecimiento->id,
                'aplicacion_web_id' => $request->aplicacion_web_id,
                'membresia_id' => $membresia->id,
                'monto_total' => $membresia->precio_membresia,
                'dias_restantes' => $membresia->duracion_membresia,
                'estado_id' => 18,
                'medio_pago_id' => 8,
            ]);


            DB::commit();
            broadcast(new UserListUpdated())->toOthers();

            return redirect()->route('login.auth')->with('success', '¡Registro exitoso! Contactanos para activar tu cuenta.');

        } catch (Exception $e) {

            DB::rollBack();


            return back()->with('error', 'Ocurrió un error inesperado durante el registro. Por favor, inténtalo de nuevo.');
        }
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
}
