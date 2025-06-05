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
use App\Models\ClienteFixgi;
use App\Mail\RecuperarPassword;

class LinkRecuperacionController extends Controller
{
    public function show()
    {
        return Inertia::render('Core/Auth/LinkRecuperacion');
    }

    public function LinkRecuperacion(Request $request)
    {
        $request->validate([
            'correo_vinculado' => 'required|email|exists:clientes_fixgis,email_ct',
        ], [
            'correo_vinculado.exists' => 'Verifica, ese usuario no existe.',
            'correo_vinculado.required' => 'El correo es requerido.',
        ]);

        $correo = $request->correo_vinculado;
        $cliente = ClienteFixgi::where('email_ct', $correo)->first();
        

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

        Mail::to($correo)->send(new RecuperarPassword($token, $correo, $cliente->nombres_ct));


        return redirect()->back()->with('success', 'Se ha enviado el enlace a tu correo.');
    }

    public function showResetForm($token, Request $request)
    {
        $email = $request->query('email');

        return Inertia::render('Core/Auth/ResetPassword', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:clientes_fixgis,email_ct',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ], [
            'email.exists' => 'Este correo no está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.required' => 'La contraseña no puede quedar vacia.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $resetRecord = DB::table('password_reset_table')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return redirect()->back()->withErrors(['email' => 'Token inválido o expirado.']);
        }

        $cliente = ClienteFixgi::where('email_ct', $request->email)->first();

        if (!$cliente) {
            return redirect()->back()->withErrors(['email' => 'Cliente no encontrado.']);
        }

        // Actualizamos la contraseña
        $cliente->contrasenia_ct = Hash::make($request->password);
        $cliente->save();


        // Actualizamos la contraseña
        $cliente->contrasenia_ct = Hash::make($request->password);
        $cliente->save();

        // Borramos el token de recuperación
        DB::table('password_reset_table')->where('email', $request->email)->delete();

        return redirect()->route('login.auth')->with('success', '¡Contraseña restablecida correctamente!');
    }
}
