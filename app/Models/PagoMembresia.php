<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoMembresia extends Model
{
    protected $table = 'pagos_membresia';

    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_pago';
    
    protected $fillable = [
        'id_cliente',
        'id_tienda',
        'monto_total',
        'id_medio_pago',
        'id_estado',
        'fecha_pago',
        'fecha_activacion' // ✅ nuevo
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteFixgi::class, 'id_cliente');
    }

    public function tienda()
    {
        return $this->belongsTo(TiendaSistematizada::class, 'id_tienda');
    }

    public function medioPago()
    {
        return $this->belongsTo(MedioPago::class, 'id_medio_pago');
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'id_estado');
    }

}
