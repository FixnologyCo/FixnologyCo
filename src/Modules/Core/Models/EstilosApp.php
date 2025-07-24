<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;


class EstilosApp extends Model
{
    protected $table = 'estilos_app';

    protected $fillable = [
        'color_fondo',
        'color_texto',
        'color_hover_texto',
        'color_border',
        'color_shadow',
        'icono',
    ];

    public function membresia()
    {
        return $this->hasMany(Membresias::class);
    }

    public function aplicacionWeb()
    {
        return $this->belongsTo(AplicacionesWeb::class);
    }

}
