<?php

namespace Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PerfilUsuario extends Model
{
    protected $table = 'perfil_usuario';

    protected $fillable = [
        'estado_id',
        'usuario_id',
        'rol_id',
        'indicativo_id',
        'tipo_documento_id',
        'ruta_foto',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'telefono',
        'correo',
        'genero',
        'direccion_residencia',
        'ciudad_residencia',
        'barrio_residencia',
    ];

    public function estado()
    {
        return $this->belongsTo(Estados::class);
    }

    public function usuario()
    {
        // ANÁLISIS Y CORRECCIÓN: Correcto. Es el inverso de User::perfilUsuario.
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Perfil tiene un Rol.
     */
    public function rol()
    {
        // ANÁLISIS Y CORRECCIÓN: Correcto.
        return $this->belongsTo(Roles::class, 'rol_id');
    }




public function indicativo()
    {
        return $this->belongsTo(Indicativos::class, 'indicativo_id');
    }



    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }




}
