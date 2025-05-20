<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\ClienteTaurus;
use App\Mail\RecuperarPassword;

class LinkRecuperacionController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/LinkRecuperacion');
    }

    public function LinkRecuperacion(Request $request)
    {
        $request->validate([
            'correo_vinculado' => 'required|email|exists:clientes_taurus,email_ct',
        ], [
            'correo_vinculado.exists' => 'Verifica, ese usuario no existe.',
            'correo_vinculado.required' => 'El correo es requerido.',
        ]);

        $correo = $request->correo_vinculado;
        $cliente = ClienteTaurus::where('email_ct', $correo)->first();

        if (!$cliente) {
            return back()->withErrors([
                'correo_vinculado' => 'No reconocemos ese usuario :(',
            ]);
        }

        $token = Str::random(64);

        DB::table('password_reset_table')->updateOrInsert(
            ['email' => $correo],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        Mail::to($correo)->send(new RecuperarPassword($token, $correo));


        return back()->with('success', 'Se ha enviado el enlace a tu correo.');
    }

    public function showResetForm($token, Request $request)
    {
        $email = $request->query('email');

        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:clientes_taurus,email_ct',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required'
        ], [
            'email.exists' => 'Este correo no está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $resetRecord = DB::table('password_reset_table')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Token inválido o expirado.']);
        }

        $cliente = ClienteTaurus::where('email_ct', $request->email)->first();

        if (!$cliente) {
            return back()->withErrors(['email' => 'Cliente no encontrado.']);
        }

        // Actualizamos la contraseña
        $cliente->contrasenia_ct = Hash::make($request->password);
        $cliente->save();


        // Actualizamos la contraseña
        $cliente->contrasenia_ct = Hash::make($request->password);
        $cliente->save();

        // Borramos el token de recuperación
        DB::table('password_reset_table')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', '¡Contraseña restablecida correctamente!');
    }
}
