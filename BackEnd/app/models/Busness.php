<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Busness extends Model
{
   protected $table = "emitentes";

   protected $guarded = [
      'id',
   ];
}
