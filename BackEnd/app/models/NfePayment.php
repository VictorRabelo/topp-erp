<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class NfePayment extends Model
{
   protected $table = "nfe_payments";

   protected $fillable = [
      'nfe_id',   'forma_id',   'forma',   'valor',   'obs',
   ];

   public function payment()
   {
      return $this->belongsTo('App\models\Payment', 'forma_id');
   }
}
