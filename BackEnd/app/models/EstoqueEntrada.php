<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class EstoqueEntrada extends Model
{
   protected $table = "estoque_entrada";

   protected $fillable = [
      'produto_id', 'valor_unitario', 'quantidade', 'nota',
   ];
}
