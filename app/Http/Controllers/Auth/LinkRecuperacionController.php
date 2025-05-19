<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\ClientesTaurus; // o el modelo correspondiente al usuario
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
            'correo_vinculado' => 'required|email|exists:clientes_taurus,email',

        ]);

        $token = Str::random(64);
        $correo = $request->correo_vinculado;

        // Guardamos el token en la tabla password_resets personalizada
        DB::table('password_resets')->updateOrInsert(
            ['email' => $correo],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // Enviar correo
        Mail::to($correo)->send(new RecuperarPassword($token));

        return back()->with('success', 'Se ha enviado el enlace a tu correo.');
    }


}
