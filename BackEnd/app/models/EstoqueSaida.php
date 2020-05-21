<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class EstoqueSaida extends Model
{
   protected $table = "estoque_saida";

   protected $fillable = [
      'produto_id', 'valor_unitario', 'quantidade', 'nota',
   ];
}
