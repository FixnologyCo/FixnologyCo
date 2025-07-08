<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class FacturacionMembresias extends Model
{
    protected $table = 'facturacion_membresias';

    
    protected $fillable = [
        'cliente_id',
        'establecimiento_id', 
        'aplicacion_web_id',
        'estado_id',
        'medio_pago_id',
        'monto_total',
        'dias_restantes',
        'fecha_pago' 
    ];

    public function cliente()
    {
        return $this->belongsTo(Usuarios::class, 'cliente_id');
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimientos::class, 'establecimiento_id');
    }

    public function aplicacionWeb()
    {
        return $this->belongsTo(AplicacionesWeb::class, 'aplicacion_web_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estados::class, 'estado_id');
    }

    public function medioPago()
    {
        return $this->belongsTo(MediosPago::class, 'medio_pago_id');
    }

}
