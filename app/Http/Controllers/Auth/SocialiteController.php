<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Core\Models\PerfilUsuario;
use Core\Models\establecimientos;
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
            // 1. Obtenemos la URL del avatar de Google al inicio.
            // Usamos getAvatar() que es el método estándar y suele dar mejor calidad.
            $originalAvatarUrl = $googleUser->getAvatar();
            $highResAvatarUrl = preg_replace('/=s\d+-c$/', '=s512-c', $originalAvatarUrl);

            $perfilExistente = PerfilUsuario::where('correo', $googleEmail)->first();
            $usuario = null;

            // Si el usuario ya existe en la base de datos...
            if ($perfilExistente) {
                $usuario = $perfilExistente->usuario;
                $eraUsuarioRecurrente = ($usuario && $usuario->google_id);

                // 2. LÓGICA PARA ACTUALIZAR LA FOTO (SI NO TIENE UNA)
                // Se comprueba si el campo de la foto está vacío o nulo.
                if ($usuario && empty($perfilExistente->ruta_foto)) {
                    $perfilExistente->ruta_foto = $highResAvatarUrl;
                    $perfilExistente->save();
                }

                // Vinculamos la cuenta si es la primera vez que inicia con Google
                if ($usuario && is_null($usuario->google_id)) {
                    $usuario->google_id = $googleId;
                    $usuario->save();
                }

                // Redireccionamos según si ya era un usuario de Google o no
                if ($eraUsuarioRecurrente) {
                    Auth::login($usuario);
                    $request->session()->regenerate();
                    return $this->validateAndRedirect($usuario);
                } else {
                    return redirect()->route('login.auth')->with('success', '¡Hemos vinculado tu cuenta de Google! ya puedes usarla.');
                }
            }
            // Si es un usuario completamente nuevo...
            else {
                $usuario = User::create([
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(24)),
                    'estado_id' => 1,
                ]);

                // Al ser nuevo, siempre se le asigna la foto de Google.
                $perfil = PerfilUsuario::create([
                    'usuario_id' => $usuario->id,
                    'correo' => $googleEmail,
                    'primer_nombre' => $googleUser->user['given_name'] ?? 'Usuario',
                    'primer_apellido' => $googleUser->user['family_name'] ?? 'NA',
                    'ruta_foto' => $highResAvatarUrl,
                ]);

                $this->crearEstructuraInicial($usuario, $perfil);

                return redirect()->route('login.auth')->with('success', '¡Tu cuenta ha sido creada con Google! Ahora contáctanos.');
            }
        } catch (Exception $e) {
            Log::error('Error en Google Callback: ' . $e->getMessage() . ' en la línea ' . $e->getLine());
            return redirect()->route('login.auth')->with('error', 'Hubo un problema al autenticar con Google, intentalo nuevamente.');
        }
    }


    /**
     * Método privado para crear la estructura inicial de un nuevo usuario.
     * Esto evita duplicar código entre el registro normal y el de Google.
     */
    protected function crearEstructuraInicial(User $usuario, PerfilUsuario $perfil)
    {

        $establecimientos = establecimientos::create([
            'estado_id' => 1,
            'aplicacion_web_id' => 1, // Asumimos que siempre es la app 1
            'propietario_id' => $usuario->id,
            'nombre_establecimiento' => 'establecimientos de ' . $perfil->primer_nombre,
            'email_establecimiento' => 'establecimientos-' . $usuario->id . '@example.com',
            'telefono_establecimiento' => '0000000000',
            'direccion_establecimiento' => 'Por definir',
            'barrio_establecimiento' => 'Por definir',
            'ciudad_establecimiento' => 'Por definir',
            'ruta_foto_establecimiento' => 'https://via.placeholder.com/150',
        ]);

        $token = TokensAcceso::create([
            'estado_id' => 2, // Inactivo por defecto
            'token_activacion' => Str::uuid(),
            'establecimiento_id' => $establecimientos->id,
        ]);

        $establecimientos->update(['token_id' => $token->id]);

        FacturacionMembresias::create([
            'cliente_id' => $usuario->id,
            'establecimiento_id' => $establecimientos->id,
            'aplicacion_web_id' => 1,
            'estado_id' => 16,
            'medio_pago_id' => 1,
            'monto_total' => 50000,
            'dias_restantes' => 7,
        ]);
    }


}