<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    protected $table = "empresas";

    protected $guarded = [
        'id',
    ];
}
