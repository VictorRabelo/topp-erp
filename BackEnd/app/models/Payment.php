<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payments_forms";

    protected $fillable = ['id', 'empresa_id', 'forma', 'parcelamento', 'max_parcelas', 'more', 'obs', 'status',];
}
