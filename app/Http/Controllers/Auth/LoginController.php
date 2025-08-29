<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\Auth\ValidatesAndRedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    use ValidatesAndRedirectsUsers;

    public function show()
    {
        return Inertia::render('Core/Auth/Auth');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'numero_documento' => 'required|string',
            'password' => 'required|string',
        ],[
            'numero_documento.required' => 'El usuario no puede quedar vacio.',
            'password.required' => 'La contraseña no puede quedar vacia.'
        ]);

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            $usuario = Auth::user();
            return $this->validateAndRedirect($usuario);
        }

        return back()->with([
            'error' => 'Las credenciales proporcionadas no son correctas.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.auth')->with('success', 'Has cerrado sesión correctamente.');
    }
}