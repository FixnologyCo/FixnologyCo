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
        return $this->belongsTo(User::class);
    }

    public function indicativo()
    {
        return $this->belongsTo(Indicativos::class);
    }

    public function rol()
    {
        return $this->belongsTo(Roles::class);
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

}
