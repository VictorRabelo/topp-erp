<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class NFCe extends Model
{
   protected $table = "nfce";

   protected $fillable = ['empresa_id', 'venda_id', 'numero', 'tpamb', 'cstatus', 'status', 'chave', 'recibo', 'protocolo', 'xjust',];
}
