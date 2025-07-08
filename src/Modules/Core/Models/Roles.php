<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'estado_id',
        'tipo_rol',
        'editar',
        'eliminar',
        'crear',
        'ver',
    ];

    public function perfil_empleado()
    {
        return $this->hasMany(PerfilEmpleado::class);
    }

    public function perfil_usuario()
    {
        return $this->hasMany(PerfilUsuario::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }
}
