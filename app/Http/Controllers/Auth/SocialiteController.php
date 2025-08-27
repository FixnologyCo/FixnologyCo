<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Core\Models\AplicacionesWeb;
use Core\Models\Membresias;
use Core\Models\PerfilUsuario;
use Core\Models\PerfilEmpleado;
use Core\Models\Establecimientos;
use Core\Models\FacturacionMembresias;
use Core\Models\TokensAcceso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Log;
use App\Http\Traits\Auth\ValidatesAndRedirectsUsers;
use Illuminate\Http\Request;
use App\Events\UserListUpdated;


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
                try {
                    do {
                        $numeroDocumento = time() . random_int(10000, 99999);

                       
                        if (strlen($numeroDocumento) > 5) {
                            $numeroDocumento = substr($numeroDocumento, 0, 5);
                        }

                    } while (User::where('numero_documento', $numeroDocumento)->exists());
                    DB::beginTransaction();
                    $usuario = User::create([
                        'google_id' => $googleId,
                        'numero_documento' => $numeroDocumento,
                        'password' => Hash::make($googleUser->user['given_name'] ?? 'Usuario'),
                        'estado_id' => 1,
                    ]);

                   
                    $perfilUsuario = PerfilUsuario::create([
                        'estado_id' => 1,
                        'usuario_id' => $usuario->id,
                        'rol_id' => 1,
                        'indicativo_id' => 1,
                        'tipo_documento_id' => 1,
                        'ruta_foto' => $highResAvatarUrl,
                        'primer_nombre' => $googleUser->user['given_name'] ?? 'Usuario',
                        'segundo_nombre' => $googleUser->user['given_name'] ?? 'NA',
                        'primer_apellido' => $googleUser->user['family_name'] ?? 'Apellido',
                        'segundo_apellido' => $googleUser->user['family_name'] ?? 'NA',
                        'telefono' => 'Por definir',
                        'correo' => $googleEmail,
                        'genero' => 'Otro',
                        'direccion_residencia' => 'Por definir',
                        'ciudad_residencia' => 'Por definir',
                        'barrio_residencia' => 'Por definir',
                    ]);

                    $establecimiento = Establecimientos::create([
                        'estado_id' => 1,
                        'aplicacion_web_id' => 1,
                        'propietario_id' => $usuario->id,
                        'ruta_foto_establecimiento' => 'https://placehold.co/400x400/f05235/FFFFFF?text=tienda id-' . $perfilUsuario->usuario_id,
                        'tipo_establecimiento' => 'Por definir',
                        'nombre_establecimiento' => 'Establecimiento de ' . $perfilUsuario->primer_nombre,
                        'email_establecimiento' => 'establecimiento-' . $usuario->id . '@example.com',
                        'telefono_establecimiento' => '0000000000',
                        'direccion_establecimiento' => 'Por definir',
                        'barrio_establecimiento' => 'Por definir',
                        'ciudad_establecimiento' => 'Por definir',
                    ]);


                    PerfilEmpleado::create([
                        'estado_id' => 1,
                        'usuario_id' => $usuario->id,
                        'establecimiento_id' => $establecimiento->id,
                        'rol_id' => $perfilUsuario->rol_id,
                        'medio_pago_id' => 1,
                        'cargo' => 'Propietario',
                        'salario_base' => 0,
                        'cuenta_ahorros' => $perfilUsuario->telefono,
                        'banco' => 'Por definir',
                        'horario' => 'Lunes a Viernes 8:00 AM - 5:00 PM',
                        'tipo_contrato' => 'Registro con google',
                        'documentos_zip' => 'NA',
                        'fecha_ingreso' => $usuario->created_at,
                    ]);


                    // ✅ Crear Token de Activación
                    $token = TokensAcceso::create([
                        'estado_id' => 2,
                        'establecimiento_id' => $establecimiento->id,
                        'token_activacion' => Str::uuid(),
                    ]);

                    // ✅ Vincular Token al Establecimiento
                    $establecimiento->update(['token_id' => $token->id]);


                    FacturacionMembresias::create([
                        'cliente_id' => $usuario->id,
                        'establecimiento_id' => $establecimiento->id,
                        'aplicacion_web_id' => 1,
                        'monto_total' => 50000,
                        'dias_restantes' => 7,
                        'estado_id' => 18,
                        'medio_pago_id' => 8,
                    ]);


                    DB::commit();
                    broadcast(new UserListUpdated())->toOthers();

                    return redirect()->route('login.auth')->with('success', '¡Tu cuenta ha sido creada con Google! Ahora contáctanos.');

                } catch (Exception $e) {
                    Log::error('Error en Google Callback: ' . $e->getMessage() . ' en la línea ' . $e->getLine());
                    return redirect()->route('login.auth')->with('error', 'Hubo un problema al crear la cuenta con Google, intentalo nuevamente.');
                }
            }

        } catch (Exception $e) {
            Log::error('Error en Google Callback: ' . $e->getMessage() . ' en la línea ' . $e->getLine());
            return redirect()->route('login.auth')->with('error', 'Hubo un problema al autenticar con Google, intentalo nuevamente.');
        }
    }



}