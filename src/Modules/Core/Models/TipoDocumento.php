<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documentos';

    protected $fillable = [
        'documento_legal',
    ];

    // Si hay relación inversa
    // Relación inversa con ClienteTaurus
    public function clientes()
    {
        return $this->hasMany(ClienteFixgi::class, 'id_tipo_documento');
    }
}
