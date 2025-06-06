<?php

namespace Core\Http\Controllers;

use Core\Models\TipoDocumento;
use Inertia\Inertia;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        $tiposDocumento = TipoDocumento::all();

        return Inertia::render('Core/Auth/Registro', [
            'tiposDocumento' => $tiposDocumento
        ]);
    }
}
