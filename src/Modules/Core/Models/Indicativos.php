<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;


class Indicativos extends Model
{
    protected $table = 'indicativos';

    protected $fillable = [
        'pais',
        'ciudad_principal',
        'codigo_pais',
        'zona_horaria',
    ];

       public function perfil_usuario()
    {
        return $this->hasMany(PerfilUsuario::class);
    }

}
