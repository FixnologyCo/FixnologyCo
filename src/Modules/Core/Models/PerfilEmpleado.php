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

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function establecimiento()
{
    return $this->belongsTo(Establecimientos::class, 'establecimiento_id');
}

    public function rol()
    {
        return $this->belongsTo(Roles::class);
    }

    public function medioPago()
    {
        return $this->belongsTo(MediosPago::class);
    }

    
}
