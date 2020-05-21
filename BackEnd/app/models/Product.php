<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   protected $table = "Produtos";

   protected $fillable = [
      'empresa_id', 'codigo_barras', 'referencia', 'descricao', 'custo', 'margem',
      'preco', 'estoque', 'un', 'origin', 'ncm', 'cfop', 'cst', 'foto',
   ];
}
