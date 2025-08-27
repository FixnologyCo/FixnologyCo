<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class HomeController extends Controller
{
    public function index()
    {
        // Verificamos si el usuario está autenticado
        if (Auth::check()) {
            // Si está autenticado, cargamos todos sus datos
            $usuario = Auth::user()->load([
                'perfilUsuario.rol',
                'establecimientoAsignado.aplicacionWeb',
            ]);

            // Y renderizamos la vista pasándole el objeto 'usuario' como prop
            return Inertia::render('Home/Index', [
                'usuario' => $usuario
            ]);

        } else {
           
            return Inertia::render('Home/Index');
        }
    }
}
