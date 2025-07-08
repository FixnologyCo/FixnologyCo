<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class TokensAcceso extends Model
{
    protected $table = 'token_accesos';


    protected $fillable = [
        'estado_id',
        'establecimiento_id',
        'token_activacion',
    ];

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'id_estado');
    }

     // Relación con TiendaSistematizada (cada token pertenece a una tienda)
     public function establecimiento()
     {
         return $this->belongsTo(Establecimientos::class, 'establecimiento_id');
     }
}
