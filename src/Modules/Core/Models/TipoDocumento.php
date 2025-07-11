<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documentos';

    protected $fillable = [
        'documento_legal',
    ];

    public function perfilUsuario()
    {
        return $this->hasMany(PerfilUsuario::class);
    }
}
