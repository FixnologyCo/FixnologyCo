<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Core\Models\PerfilUsuario;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\RecuperarPassword;

class LinkRecuperacionController extends Controller
{
    public function show()
    {
        return Inertia::render('Core/Auth/LinkRecuperacion');
    }

    public function LinkRecuperacion(Request $request)
    {
        $request->validate(
            ['correo' => 'required|email|exists:perfil_usuario,correo'],
            [
                'correo.required' => 'Debes ingresar un correo.', 
                'correo.exists' => 'Oh, oh... Desconocemos ese correo.',
                'correo.email' => 'Debe ser SÍ o SÍ un correo.',
            ],

        );

        $perfil = PerfilUsuario::where('correo', $request->correo)->first();

        if (!$perfil) {
            return back()->withErrors(['correo' => 'No se encontró un usuario con ese correo.']);
        }

        $nombre = $perfil->primer_nombre;
        $token = Str::random(60);


        DB::table('password_resets')->insert([
            'correo' => $request->correo,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($perfil->correo)->send(new RecuperarPassword($token, $perfil->correo, $nombre));

        return redirect()->back()->with('success', $nombre . ', se ha enviado el enlace a tu correo vinculado.');
    }

    public function showResetForm($token, Request $request)
    {

        $resetRecord = DB::table('password_resets')->where('token', $token)->first();

        // Si el token no existe, redirigimos con un error
        if (!$resetRecord) {
            return redirect()->route('login.auth')->with('error', 'Este enlace de recuperación no es válido o ya fue utilizado.');
        }


        $tokenCreatedAt = Carbon::parse($resetRecord->created_at);
        $minutesSinceCreation = $tokenCreatedAt->diffInMinutes(Carbon::now());


        if ($minutesSinceCreation > 5) {

            DB::table('password_resets')->where('token', $token)->delete();

            return redirect()->route('login.auth')
                ->with('error', 'Tu enlace de recuperación ha expirado. Por favor, solicita uno nuevo.');
        }


        return Inertia::render('Core/Auth/ResetPassword', [
            'token' => $token,
            'correo' => $resetRecord->correo,
        ]);
    }

    public function reset(Request $request)
    {
        // 1. Validación (añadimos la regla 'confirmed' para la contraseña)
        $request->validate([
            'correo' => 'required|email|exists:perfil_usuario,correo',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' busca un campo 'password_confirmation'
            'token' => 'required|string'
        ], [
            'correo.exists' => 'Este correo no está registrado.',
            'password.required' => 'La contraseña no puede quedar vacía.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ]);

        // 2. Verificación del token
        $resetRecord = DB::table('password_resets')
            ->where('correo', $request->correo)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['correo' => 'Este enlace de recuperación no es válido o ya ha expirado.']);
        }

        // 3. Encontrar al usuario CORRECTO
        $perfil = PerfilUsuario::where('correo', $request->correo)->first();

        if (!$perfil || !$perfil->usuario) {
            return back()->withErrors(['correo' => 'Error: No se encontró un usuario asociado a este correo.']);
        }

        $usuario = $perfil->usuario;

        // 4. Actualizar la contraseña
        $usuario->password = Hash::make($request->password);
        $usuario->save();

        // 5. Eliminar el token (con el nombre de tabla correcto)
        DB::table('password_resets')->where('correo', $request->correo)->delete();

        // 6. Redirigir con éxito
        return redirect()->route('login.auth')->with('success', '¡Tu contraseña ha sido restablecida! Ya puedes iniciar sesión.');
    }
}
