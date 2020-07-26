<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeItens extends Model
{
   protected $table = "nfe_itens";

   protected $fillable = [
      'nfe_id',   'produto_id',   'descricao',   'quantidade',   'valor_unitario',   'desconto',   'total'
   ];

   public function produto()
   {
      return $this->belongsTo('App\models\Product', 'produto_id');
   }
}
