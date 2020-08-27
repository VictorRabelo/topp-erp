<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfePayment extends Model
{
   protected $table = "nfe_payments";

   protected $fillable = [
      'nfe_id',   'forma_id',   'forma',   'valor',   'obs',
   ];

   public function payment()
   {
      return $this->belongsTo('App\Models\Payment', 'forma_id');
   }
}
