<?php

namespace Core\Models;

use App\Models\User;
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
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function establecimientos()
    {
        return $this->belongsTo(Establecimientos::class, 'establecimiento_id');
    }

    public function aplicacionWeb()
    {
        return $this->belongsTo(AplicacionesWeb::class, 'aplicacion_web_id');
    }

    public function estado()
    {
        // ANÁLISIS Y CORRECCIÓN: Cambiado de hasOne a belongsTo.
        // Una factura "pertenece a" un estado, no "tiene uno".
        // La tabla 'facturacion_membresias' tiene la columna 'estado_id'.
        return $this->belongsTo(Estados::class, 'estado_id');
    }

    public function medioPago()
    {
        return $this->belongsTo(MediosPago::class, 'medio_pago_id');
    }

}
