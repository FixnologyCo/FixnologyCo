<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;


class EstilosApp extends Model
{
    protected $table = 'estilos_app';

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
        return $this->belongsTo(Estilos::class, 'estilo_id');
    }

    public function membresia()
    {
        return $this->belongsTo(Membresias::class, 'membresia_id');
    }

}
