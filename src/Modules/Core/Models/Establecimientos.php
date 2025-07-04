<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class Establecimientos extends Model
{
    protected $table = 'establecimientos';

    protected $fillable = [
        'estado_id',
        'token_id',
        'aplicacion_web_id',
        'propietario_id',
        'ruta_foto_establecimiento',
        'tipo_establecimiento',
        'nombre_establecimiento',
        'email_establecimiento',
        'telefono_establecimiento',
        'direccion_establecimiento',
        'barrio_establecimiento',
        'ciudad_establecimiento',
    ];

    protected $with = [
        'estado',
    ];


    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }

    public function token()
    {
        return $this->belongsTo(TokenAcceso::class, 'token_id');
    }
    

    public function aplicacion()
    {
        return $this->belongsTo(AplicacionWeb::class, 'aplicacion_web_id', 'id');
    }

    public function propietario()
    {
        return $this->belongsTo(Usuarios::class, 'propietario_id');
    }
}
