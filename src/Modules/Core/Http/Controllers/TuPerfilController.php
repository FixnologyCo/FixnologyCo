<?php

namespace Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;


class TuPerfilController extends Controller
{
    /**
     * Actualiza la foto del perfil del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */


    public function actualizarFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image',
        ]);


        if ($request->hasFile('foto') && $request->file('foto')->getSize() > 3048 * 1024) {
            return redirect()->back()->with([
                'error' => 'La foto no puede superar los 3MB.',
            ]);
        }

        $cliente = Auth::user();
        $foto = $request->file('foto');

        // Guardar como binario
        $cliente->foto_binaria = base64_encode(file_get_contents($foto->getRealPath()));
        $cliente->save();   


        return redirect()->back()->with('success', 'Foto de perfil actualizada correctamente.');
    }



}

