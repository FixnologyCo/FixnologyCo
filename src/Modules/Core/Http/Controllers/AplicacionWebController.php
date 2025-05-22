<?php

namespace Core\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\AplicacionWeb;

class AplicacionWebController extends Controller
{
    public function index()
    {
        $aplicaciones = AplicacionWeb::where('id_membresia', 1)->get();

        return Inertia::render('Core/Auth/Registro', [
            'aplicaciones' => $aplicaciones
        ]);
    }
}
