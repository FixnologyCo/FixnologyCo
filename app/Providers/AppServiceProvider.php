<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia; // 👉 Asegúrate de que esta línea esté presente
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ Definir el Gate para roles
        Gate::define('access-role', function (User $user, $role) {
            return $user->id_rol == $role;
        });

        Inertia::share([
            'auth' => function () {
                if (Auth::check()) {
                    // ✅ Cargar todas las relaciones correctamente
                    $user = Auth::user()->load('rol', 'estado', 'establecimientos.estado');

                    return [
                        'user' => [
                            'id' => $user->id,
                            'nombres_ct' => $user->nombres_ct,
                            'apellidos_ct' => $user->apellidos_ct,
                            'email_ct' => $user->email_ct,
                            'telefono_ct' => $user->telefono_ct,
                            'rol' => $user->rol ? [
                                'id' => $user->rol->id,
                                'nombre' => $user->rol->nombre
                            ] : null,
                            'estado' => $user->estado ? [
                                'id' => $user->estado->id,
                                'nombre' => $user->estado->tipo_estado 
                            ] : null,
                            'establecimiento' => $user->establecimientos ? [
                                'nombre_establecimiento' => $user->establecimientos->nombre_establecimiento,
                                'email_establecimiento' => $user->establecimientos->email_establecimiento,
                                'telefono_establecimiento' => $user->establecimientos->telefono_ct,
                                'direccion' => $user->establecimientos->direccion_ct,
                                'barrio' => $user->establecimientos->barrio_ct,
                                'estado' => $user->establecimientos->estado ? [
                                    'id' => $user->establecimientos->estado->id,
                                    'nombre' => $user->establecimientos->estado->tipo_estado
                                ] : null,
                                'created_at' => $user->establecimientos->created_at,
                                'logo_establecimiento' => $user->establecimientos->logo_establecimiento
                                    ? url('storage/' . $user->establecimientos->logo_establecimiento)
                                    : null
                            ] : null
                        ]
                    ];
                }
                return null;
            },

             // Compartir mensajes flash para éxito y error
             'flash' => function () {
                return [
                    'success' => session('success'),
                    'error'   => session('error'),
                ];
            },
        ]);
    }
}
