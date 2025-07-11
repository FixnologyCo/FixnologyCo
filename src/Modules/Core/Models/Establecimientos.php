<?php

namespace Core\Models;

use App\Models\User;
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
        return $this->belongsTo(Estados::class);
    }

    public function token()
    {
        return $this->belongsTo(TokensAcceso::class, 'token_id');
    }

    public function aplicacionWeb()
    {
        return $this->belongsTo(AplicacionesWeb::class, 'aplicacion_web_id');
    }

    public function facturas()
    {
        // Un establecimiento tiene muchas facturas
        return $this->hasMany(FacturacionMembresias::class, 'establecimiento_id');
    }

    public function propietario()
    {
        return $this->belongsTo(User::class);
    }
}
