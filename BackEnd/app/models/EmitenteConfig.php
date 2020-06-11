<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class EmitenteConfig extends Model
{
   protected $table = "emitentes_config";

   protected $fillable = [
      'emitente_id', 'modelo', 'seq', 'seqHomolog', 'serie', 'serieHomolog',
      'tpnf', 'tpemiss', 'tpimp', 'tpamb', 'csc', 'cscHomolog', 'cscid', 'cscidHomolog',
   ];
}
