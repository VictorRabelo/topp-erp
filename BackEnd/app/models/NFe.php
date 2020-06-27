<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class NFe extends Model
{
   protected $table = "nfe";

   protected $guarded = ['id'];

   public function emitente()
   {
      return $this->belongsTo('App\models\Emitente', 'emitente_id');
   }

   public function venda()
   {
      return $this->belongsTo('App\models\Venda', 'venda_id');
   }
}
