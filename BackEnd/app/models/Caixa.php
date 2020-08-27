<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
   protected $table = "caixa";

   protected $fillable = [
      'empresa_id', 'cliente_id', 'venda_id', 'tipo', 'descricao', 'valor',
   ];

   public function cliente()
   {
      return $this->belongsTo('App\Models\Client', 'client_id');
   }

   public function venda()
   {
      return $this->belongsTo('App\Models\Venda', 'venda_id');
   }
}
