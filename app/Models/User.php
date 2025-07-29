<?php

// RUTA: app/Models/User.php

namespace App\Models;

use Core\Models\Establecimientos;
use Core\Models\Estados;
use Core\Models\PerfilEmpleado;
use Core\Models\PerfilUsuario;
use Core\Models\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $fillable = ['id', 'numero_documento', 'password', 'estado_id', 'google_id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed'];

    // --- RELACIONES CORREGIDAS Y SIMPLIFICADAS ---

    /**
     * Un usuario tiene un perfil con sus datos personales.
     * Relación: Uno a Uno.
     */
    public function perfilUsuario()
    {
        return $this->hasOne(PerfilUsuario::class, 'usuario_id');
    }

    /**
     * Un usuario puede tener datos de empleado.
     * ANÁLISIS Y CORRECCIÓN: Esta es una relación directa. La tabla 'perfil_empleado'
     * tiene una columna 'usuario_id'. Esta es la relación que usaremos para la consulta.
     * Es la más simple y directa según tu estructura.
     */
    public function perfilEmpleado()
    {
        return $this->hasOne(PerfilEmpleado::class, 'usuario_id');
    }

    /**
     * Un usuario puede ser propietario de MUCHOS establecimientos.
     * ANÁLISIS Y CORRECCIÓN: El nombre de la relación DEBE ser plural ('establecimientos')
     * porque el tipo de relación es hasMany (tiene MUCHOS). Esta era una fuente principal de errores.
     */
    public function establecimientos()
    {
        return $this->hasOne(Establecimientos::class, 'propietario_id');
    }

    public function establecimientoAsignado()
    {
        return $this->hasOneThrough(
            Establecimientos::class, // 1. El modelo final al que queremos llegar.
            PerfilEmpleado::class,   // 2. El modelo intermedio que debemos atravesar.
            'usuario_id',          // 3. Llave foránea en la tabla intermedia (perfil_empleado) que se conecta con User.
            'id',                  // 4. Llave foránea en la tabla final (establecimientos) que se conecta con la intermedia.
            'id',                  // 5. Llave local en este modelo (users).
            'establecimiento_id'   // 6. Llave foránea en la tabla intermedia (perfil_empleado) que se conecta con la final.
        );
    }

    /**
     * El estado de un usuario (activo, inactivo, etc.).
     */
    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }

    /**
     * El rol de un usuario se obtiene a través de su perfil.
     * ANÁLISIS Y CORRECCIÓN: Este método se deja como un "accesor" conveniente.
     * NO es una relación cargable con with() o load(), pero es útil para acceder
     * al rol de forma sencilla: $usuario->getRol()
     */
    public function rol()
    {
        // Se usa un método en lugar de la propiedad mágica para evitar conflictos.
        return $this->belongsTo(Roles::class, 'rol_id');
    }
}