<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeReferences extends Model
{
    public $timestamps = false;

    protected $table = "nfe_references";

    protected $fillable = [
        'nfe_id', 'numero_nfe', 'chave_nfe',
    ];
}
