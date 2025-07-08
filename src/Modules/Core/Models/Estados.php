<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class Estados extends Model
{
    protected $table = 'estados';

    protected $fillable = [
        'tipo_estado',
        'categoria_estado',
    ];

    public function facturacionMembresias()
    {
        return $this->hasMany(FaturacionMembresias::class);
    }

    public function mediosPago(){
        return $this->hasMany(MediosPago::class);
    }

    public function membresias()
    {
        return $this->hasMany(Membresias::class);
    }

    public function aplicacionesWeb()
    {
        return $this->hasMany(AplicacionWeb::class);
    }

    public function establecimientos()
    {
        return $this->hasMany(Establecimientos::class);
    }

    public function perfil_empleado()
    {
        return $this->hasMany(PerfilEmpleado::class);
    }

    public function perfil_usuario()
    {
        return $this->hasMany(PerfilUsuario::class);
    }

    public function tokensAcceso()
    {
        return $this->hasMany(TokensAcceso::class);
    }

    public function roles()
    {
        return $this->hasMany(Roles::class);
    }

    


}

// eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30