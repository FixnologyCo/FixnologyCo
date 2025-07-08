<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class Membresias extends Model
{
    protected $table = 'membresias';

    protected $fillable = [
        'estado_id',
        'estilo_id',
        'nombre_membresia',
        'precio_membresia',
        'periodo_duracion',
        'duracion_membresia',
        'descripcion_corta',
    ];

    // ✅ Relación con estado
    public function estado()
    {
        return $this->belongsTo(Estados::class);
    }

    public function estilo()
    {
        return $this->belongsTo(EstilosApp::class);
    }

}
