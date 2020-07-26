<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendaItens extends Model
{
   protected $table = "venda_itens";

   protected $fillable = [
      'venda_id',   'produto_id',   'descricao',   'quantidade',   'valor_unitario',   'desconto',   'total'
   ];

   public function produto()
   {
      return $this->belongsTo('App\models\Product', 'produto_id');
   }
}
