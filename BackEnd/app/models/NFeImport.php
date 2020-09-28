<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFeImport extends Model
{
    protected $table = "nfe_import";

    protected $fillable = [
        'empresa_id', 'chave', 'numero_nfe',
    ];
}
