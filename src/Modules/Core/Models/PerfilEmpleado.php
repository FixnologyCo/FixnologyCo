<?php

namespace Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PerfilEmpleado extends Model
{
    protected $table = 'perfil_empleado';

    protected $fillable = [
        'estado_id',
        'usuario_id',
        'establecimiento_id',
        'rol_id',
        'medio_pago_id',
        'cargo',
        'salario_base',
        'cuenta_ahorros',
        'banco',
        'horario',
        'tipo_contrato',
        'documentos_zip',
        'fecha_ingreso',
        'fecha_egreso'
    ];

    public function estado()
    {
        return $this->belongsTo(Estados::class);
    }

    /**
     * Los datos de empleado pertenecen a un Usuario.
     */
    public function usuario()
    {
        // ANÁLISIS Y CORRECCIÓN: Se restaura la relación directa con User,
        // que coincide con la estructura de la BD y el error SQL.
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function establecimientos()
    {
        // ANÁLISIS Y CORRECCIÓN: Correcto.
        return $this->belongsTo(Establecimientos::class, 'establecimiento_id');
    }
    public function rol()
    {
        // ANÁLISIS Y CORRECCIÓN: Correcto.
        return $this->belongsTo(Roles::class, 'rol_id');
    }

    public function medioPago()
    {
        return $this->belongsTo(MediosPago::class);
    }
}
