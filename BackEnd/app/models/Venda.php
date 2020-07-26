<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
   protected $table = "vendas";

   protected $fillable = [
      'empresa_id', 'user_id', 'cliente_id', 'cliente', 'cpf', 'subtotal', 'desconto', 'total', 'status',
   ];

}
