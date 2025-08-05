<?php

namespace Core\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Importar
use Illuminate\Database\Eloquent\Model;

class TokensAcceso extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'token_accesos';


    protected $fillable = [
        'estado_id',
        'establecimiento_id',
        'token_activacion',
    ];

    public function estado()
    {
        return $this->belongsTo(Estados::class);
    }

     public function establecimientos()
     {
         return $this->belongsTo(Establecimientos::class);
     }
}
