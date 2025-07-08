<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class MediosPago extends Model
{
    protected $table = 'medios_pago';


    protected $fillable = [
        'estado_id',
        'forma_pago'
    ];

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }
}
