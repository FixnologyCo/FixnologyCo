<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;


class AplicacionesWeb extends Model
{
    protected $table = 'aplicaciones_web';

    protected $fillable = [
        'estado_id',
        'estilo_id',
        'membresia_id',
        'nombre_app',
        'categoria_app',
        'descripcion_app',
    ];

    public function estado()
    {
        return $this->belongsTo(Estados::class);
    }

    public function estilo()
    {
        return $this->belongsTo(EstilosApp::class);
    }

    public function membresia()
{
    return $this->belongsTo(FacturacionMembresias::class, 'membresia_id');
}

}
