<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class BusnessConfig extends Model
{
   protected $table = "emitentes_config";

   protected $guarded = [
      'id',
   ];
}
