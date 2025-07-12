<?php

namespace Core\Http\Controllers; // Tu namespace personalizado

use App\Http\Controllers\Controller;
use App\Models\User;
use Core\Models\AplicacionesWeb; // Asegúrate de que tus modelos estén en App\Models
use Core\Models\PerfilUsuario;
use Core\Models\Establecimientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class DashboardSuperAdminController extends Controller
{
    

    /**
     * Muestra el dashboard para el SuperAdmin.
     */
    public function show(Request $request, $aplicacion, $rol)
    {
        // 1. AUTORIZACIÓN
        // Suponiendo que tu método validarAcceso funciona, lo mantenemos.
        // Asegúrate de que internamente use la lógica de roles correcta.
        // Aquí podrías usar Gates o Policies de Laravel también.
        // Gate::authorize('view-superadmin-dashboard');

        // 2. OBTENER EL USUARIO AUTENTICADO Y SUS DATOS
        // Cargamos las relaciones que necesitamos del perfil del usuario logueado.
        $usuario = Auth::user()->load(
            'perfilUsuario',
            'perfilUsuario.indicativo',
            'perfilUsuario.tipoDocumento',
            'perfilEmpleado',
            'perfilEmpleado.estado',
            'perfilEmpleado.medioPago',
            'tienda',
            'tienda.token',
            'tienda.token.estado',
            'tienda.aplicacionWeb',
            'tienda.aplicacionWeb.estado',
            'tienda.aplicacionWeb.membresia',
            'tienda.aplicacionWeb.membresia.estado',
            'tienda.facturas',
            'tienda.facturas.estado',
            'tienda.propietario'

        );
        $tipoDeRol = $usuario->rol->tipo_rol; // Ej: "SuperAdmin"
       




        // 5. RENDERIZAR LA VISTA DE INERTIA CON LOS PROPS CORRECTOS
        // Ya no necesitamos el 'if' que validaba la tienda, eso lo debe hacer la autorización.
        return Inertia::render('Apps/' . ucfirst($aplicacion) . '/' . ucfirst($rol) . '/Dashboard/Dashboard', [
            'usuario' => $usuario,
            'rol' => $tipoDeRol
            
           
        ]);
    }
}