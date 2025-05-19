<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Models\ClientesTaurus;

class ResetPasswordController extends Controller
{
    public function showResetForm($token)
{
    return Inertia::render('Auth/ResetPassword', ['token' => $token]);
}

public function reset(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:clientes_taurus,email',
        'password' => 'required|min:6|confirmed',
        'token' => 'required'
    ]);

    $reset = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->first();

    if (!$reset || Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
        return back()->withErrors(['email' => 'Token inválido o expirado.']);
    }

    $user = ClientesTaurus::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('success', 'Tu contraseña ha sido restablecida.');
}
}
