<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Core\Models\PerfilUsuario;
use Core\Models\Establecimientos;
use Core\Models\FacturacionMembresias;
use Core\Models\TokensAcceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Log;
use App\Http\Traits\Auth\ValidatesAndRedirectsUsers;
use Illuminate\Http\Request;


class SocialiteController extends Controller
{
    use ValidatesAndRedirectsUsers;
    /**
     * Redirige al usuario a la página de autenticación de Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja la respuesta (callback) de Google después de la autenticación.
     */
    // En tu SocialiteController, reemplaza el método handleGoogleCallback con este:

  public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $googleEmail = $googleUser->getEmail();
            $googleId = $googleUser->getId();

            $perfilExistente = PerfilUsuario::where('correo', $googleEmail)->first();
            $usuario = null;

            if ($perfilExistente) {
                $usuario = $perfilExistente->usuario;
                $eraUsuarioRecurrente = ($usuario && $usuario->google_id);

                if ($usuario && is_null($usuario->google_id)) {
                    $usuario->google_id = $googleId;
                    $usuario->save();
                }

                if ($eraUsuarioRecurrente) {
                    Auth::login($usuario);
                    $request->session()->regenerate();
                    return $this->validateAndRedirect($usuario); // <-- Esto ahora funciona
                } else {
                   return redirect()->route('login.auth')->with('success', '¡Hemos vinculado tu cuenta de Google! Ahora contáctanos.');
                }
            } else {
                $usuario = User::create([
                    'google_id' => $googleId,
                    'password'  => Hash::make(Str::random(24)),
                    'estado_id' => 1,
                ]);

                $perfil = PerfilUsuario::create([
                    'usuario_id'      => $usuario->id,
                    'correo'          => $googleEmail,
                    'primer_nombre'   => $googleUser->user['given_name'] ?? 'Usuario',
                    'primer_apellido' => $googleUser->user['family_name'] ?? 'Google',
                    'ruta_foto'       => $googleUser->getAvatar(),
                ]);

                $this->crearEstructuraInicial($usuario, $perfil);

                return redirect()->route('login.auth')->with('success', '¡Tu cuenta ha sido creada con Google! Ahora contáctanos.');
            }
        } catch (Exception $e) {
            Log::error('Error en Google Callback: ' . $e->getMessage() . ' en la línea ' . $e->getLine());
           return redirect()->route('login.auth')->with('error', 'Hubo un problema al autenticar con Google, ponte en contacto con nosotros.');
        }
    }


    /**
     * Método privado para crear la estructura inicial de un nuevo usuario.
     * Esto evita duplicar código entre el registro normal y el de Google.
     */
    protected function crearEstructuraInicial(User $usuario, PerfilUsuario $perfil)
    {

        $establecimiento = Establecimientos::create([
            'estado_id' => 1,
            'aplicacion_web_id' => 1, // Asumimos que siempre es la app 1
            'propietario_id' => $usuario->id,
            'nombre_establecimiento' => 'Tienda de ' . $perfil->primer_nombre,
            'email_establecimiento' => 'tienda-' . $usuario->id . '@example.com', // Email único temporal
            'telefono_establecimiento' => '0000000000', // Teléfono temporal
            'direccion_establecimiento' => 'Por definir',
            'barrio_establecimiento' => 'Por definir',
            'ciudad_establecimiento' => 'Por definir',
            'ruta_foto_establecimiento' => 'https://via.placeholder.com/150',
        ]);

        $token = TokensAcceso::create([
            'estado_id' => 2, // Inactivo por defecto
            'token_activacion' => Str::uuid(),
            'establecimiento_id' => $establecimiento->id,
        ]);

        $establecimiento->update(['token_id' => $token->id]);

        FacturacionMembresias::create([
            'cliente_id' => $usuario->id,
            'establecimiento_id' => $establecimiento->id,
            'aplicacion_web_id' => 1,
            'estado_id' => 16, // Pendiente
            'medio_pago_id' => 1, // Efectivo por defecto
            'monto_total' => 50000,
            'dias_restantes' => 7, // Días de prueba
        ]);
    }


}