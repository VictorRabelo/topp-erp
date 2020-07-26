<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "produtos";

    protected $fillable = [
        'empresa_id', 'codigo_barras', 'referencia', 'descricao', 'custo', 'margem',
        'preco', 'estoque', 'un', 'origin', 'ncm', 'cfop', 'cst', 'foto', 'tipo',
    ];
}
