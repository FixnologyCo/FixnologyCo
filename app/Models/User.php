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
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';
    protected $fillable = ['id', 'numero_documento', 'password', 'estado_id', 'google_id'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed'];

    public function perfilUsuario()
    {
        return $this->hasOne(PerfilUsuario::class, 'usuario_id');
    }

    public function perfilEmpleado()
    {
        return $this->hasOne(PerfilEmpleado::class, 'usuario_id');
    }

    public function establecimiento()
    {
        return $this->hasOne(Establecimientos::class, 'propietario_id');
    }

    public function establecimientoAsignado()
    {
        return $this->hasOneThrough(
            Establecimientos::class, 
            PerfilEmpleado::class,  
            'usuario_id',
            'id',
            'id',
            'establecimiento_id'
        );
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }

    public function rol()
    {
        return $this->belongsTo(Roles::class, 'rol_id');
    }

    
}