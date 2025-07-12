<?php

// El namespace DEBE coincidir con la estructura de carpetas a partir de 'app'
namespace App\Models;

use Core\Models\Establecimientos;
use Core\Models\Estados;
use Core\Models\PerfilEmpleado;
use Core\Models\PerfilUsuario;
use Core\Models\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Aquí puedes agregar tus Traits si los usas, como SoftDeletes
// use Illuminate\Database\Eloquent\SoftDeletes; 

class User extends Authenticatable // El nombre de la clase DEBE ser 'User' para coincidir con 'User.php'
{
    // Aquí puedes añadir todos los traits que necesites
    use HasFactory, Notifiable;

    /**
     * Esta línea es PERFECTA. Le dice a este modelo 'User'
     * que debe usar la tabla 'usuarios' de tu base de datos.
     */
    protected $table = 'usuarios';

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'numero_documento',
        'password',
        'estado_id',
    ];

    /**
     * Los atributos que deben estar ocultos.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser convertidos.
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    // --- Aquí defines tus relaciones ---

    public function perfilUsuario()
    {
        return $this->hasOne(PerfilUsuario::class, 'usuario_id');
    }


    public function perfilEmpleado()
    {
        return $this->hasMany(PerfilEmpleado::class, 'usuario_id');
    }

    public function tienda()
    {
        return $this->hasMany(Establecimientos::class, 'propietario_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }

  

    public function rol()
    {
        return $this->hasOneThrough(
            Roles::class,           // 1. El modelo final al que queremos llegar.
            PerfilUsuario::class, // 2. El modelo intermedio que debemos atravesar.
            'usuario_id',         // 3. La llave foránea en la tabla intermedia (`perfil_usuario`).
            'id',                 // 4. La llave foránea en la tabla final (`roles`).
            'id',                 // 5. La llave local en este modelo (`usuarios`).
            'rol_id'              // 6. La llave local en la tabla intermedia (`perfil_usuario`).
        );
    }


}