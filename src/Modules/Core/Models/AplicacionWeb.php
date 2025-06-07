<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;


class AplicacionWeb extends Model
{
    protected $table = 'aplicaciones_web';

    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_modificacion';

    protected $fillable = [
        'id_estado',
        'id_plan_aplicacion',
        'id_membresia',
        'nombre_app',
        'descripcion',
        'color_fondo',
        'icono_app',
    ];

    // ✅ Relación con el plan de aplicación
    public function plan()
    {
        return $this->belongsTo(PlanAplicacion::class, 'id_plan_aplicacion')->with('detalles');
    }

    // ✅ Relación con la membresía
    public function membresia()
    {
        return $this->belongsTo(Membresia::class, 'id_membresia');
    }

    // ✅ Relación con el estado
    public function estado()
    {
        return $this->belongsTo(Estados::class, 'id_estado');
    }

     public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function tiendas()
    {
        return $this->hasMany(TiendaSistematizada::class, 'id_aplicacion_web');
    }

    public function tienda()
    {
        return $this->belongsTo(TiendaSistematizada::class, 'id_tienda');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id');
    }

    public function pagos_membresia()
{
    return $this->hasMany(PagoMembresia::class, 'id_cliente');
}

}
