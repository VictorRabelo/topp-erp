<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFCe extends Model
{
   protected $table = "nfce";

   protected $fillable = ['empresa_id', 'emitente_id', 'venda_id', 'numero', 'tpamb', 'cstatus', 'status', 'chave', 'recibo', 'protocolo', 'xjust',];

   public function emitente()
   {
      return $this->belongsTo('App\models\Emitente', 'emitente_id');
   }

   public function venda()
   {
      return $this->belongsTo('App\models\Venda', 'venda_id');
   }
}
