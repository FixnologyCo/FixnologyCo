<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Core\Models\ClienteFixgi;
use Core\Models\TiendaSistematizada;
use Core\Models\TokenAcceso;
use Core\Models\TipoDocumento;
use Core\Models\AplicacionWeb;
use Core\Models\PagoMembresia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RegisterController extends Controller
{
    // ✅ Mostrar formulario de registro
    public function show()
    {
        $tipoDocumentos = TipoDocumento::all();
        $aplicaciones = AplicacionWeb::where('id_membresia', 1)
            ->select('id', 'nombre_app')
            ->get();

        return Inertia::render('Core/Auth/Registro', [
            'tiposDocumento' => $tipoDocumentos,
            'aplicaciones' => $aplicaciones
        ]);
    }

    // ✅ Lógica de registro
    public function register(Request $request)
    {
        $request->merge([
            'id_aplicacion' => $request->id_aplicacion ? (int) $request->id_aplicacion : null
        ]);
        // ✅ Validación de datos
        $request->validate([
            'nombres_ct' => 'required|string|max:25',
            'apellidos_ct' => 'required|string|max:25',
            'id_tipo_documento' => 'required|integer|exists:tipo_documentos,id',
            'numero_documento_ct' => 'required|string|max:15|unique:clientes_fixgis,numero_documento_ct',
            'email_ct' => 'required|string|email|max:60|unique:clientes_fixgis,email_ct',
            'telefono_ct' => 'required|string|max:10|min:10|unique:clientes_fixgis,telefono_ct',
            'contrasenia_ct' => 'required|string|min:6|confirmed',
            'id_aplicacion' => 'required|nullable|integer|exists:aplicaciones_web,id',

        ], [
            'nombres_ct.required' => 'Los nombres son requeridos.',
            'apellidos_ct.required' => 'Los apellidos son requeridos.',
            'id_tipo_documento.required' => 'El tipo de documento es requerido.',
            'numero_documento_ct.required' => 'El número de documento es requerido.',
            'numero_documento_ct.unique' => 'Oh, oh. Este documento ya existe',
            'email_ct.required' => 'El email es requerido.',
            'email_ct.unique' => 'Oh, oh, este correo ya se encuentra vinculado.',
            'telefono_ct.required' => 'El teléfono es requerido.',
            'telefono_ct.max' => 'El teléfono debe ser de maximo 10 digitos.',
            'telefono_ct.min' => 'El teléfono debe ser de minimo 10 digitos.',
            'telefono_ct.unique' => 'Oh, oh, este telefono ya se encuentra vinculado.',
            'contrasenia_ct.required' => 'La contraseña es requerida.',
            'contrasenia_ct.min' => 'La contraseña debe ser minimo de 6 caracteres.',
            'contrasenia_ct.confirmed' => 'Las contraseñas no coinciden.',
            'id_aplicacion.required' => 'La app de interés es requerida.',
            'id_aplicacion.exists' => 'La app seleccionada no es válida.',
        ]);

        // ✅ Crear cliente
        $cliente = ClienteFixgi::create([
            'nombres_ct' => $request->nombres_ct,
            'apellidos_ct' => $request->apellidos_ct,
            'id_tipo_documento' => $request->id_tipo_documento,
            'numero_documento_ct' => $request->numero_documento_ct,
            'email_ct' => $request->email_ct,
            'telefono_ct' => $request->telefono_ct,
            'contrasenia_ct' => Hash::make($request->contrasenia_ct),
            'id_estado' => 1,
            'id_rol' => 1,
        ]);

        // ✅ Crear tienda vinculada al cliente
        $tienda = TiendaSistematizada::create([
            'id_estado' => 1,
            'id_aplicacion_web' => $request->id_aplicacion,
            'nombre_tienda' => 'Tienda de ' . $cliente->nombres_ct,
            'email_tienda' => $cliente->email_ct,
            'telefono_tienda' => $cliente->telefono_ct,
            'direccion_tienda' => 'Dirección por defecto',
            'barrio_tienda' => 'Barrio por defecto',
        ]);

        // ✅ Asignar la tienda creada al cliente
        $cliente->update(['id_tienda' => $tienda->id]);

        // ✅ Crear token automáticamente
        $token = TokenAcceso::create([
            'id_cliente_ct' => $cliente->id,
            'id_estado' => 2, 
            'token_activacion' => Str::uuid(),
            'id_tienda_sistematizada' => $tienda->id,
        ]);

        // ✅ Actualizar el id_token en la tienda para que quede vinculado
        $tienda->update(['id_token' => $token->id]);
        $montoTotal = AplicacionWeb::where('id', $request->id_aplicacion)
            ->with('membresia')
            ->first()
            ->membresia
            ->precio;

        // ✅ Crear registro de pago de membresía
        // ✅ Registrar el pago en la tabla `pagos_membresia`
        PagoMembresia::create([
            'id_cliente' => $cliente->id,
            'id_tienda' => $tienda->id,
            'id_medio_pago' => 1, //
            'id_estado' => 8, // 
            'monto_total' => $montoTotal,
            'fecha_pago' => now(),
        ]);

        // ✅ Guarda el mensaje en la sesión y redirige a login con Inertia
        return redirect()->route('login.auth')->with('success', 'Registro exitoso, Activa el token y ¡empecemos!');

    }
}
