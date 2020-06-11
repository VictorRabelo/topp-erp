<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Busness extends Model
{
   protected $table = "emitentes";

   protected $guarded = [
      'id',
   ];
}
