<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class VendasPayments extends Model
{
   protected $table = "vendas_payments";

   protected $fillable = [
      'venda_id', 'forma_id', 'forma', 'valor', 'troco', 'obs',
   ];

   public function payment()
   {
      return $this->belongsTo('App\models\Payment', 'forma_id');
   }
   public function venda()
   {
      return $this->belongsTo('App\models\Payment', 'venda_id');
   }
}
