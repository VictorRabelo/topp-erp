<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorFiscal extends Model
{
    protected $table = "monitor_fiscal";

    protected $fillable = [
        'emitente_id', 'nsu', 'numero_nfe', 'razao', 'cnpj', 'tpnf', 'valor', 'chave', 'nprot', 'cstatus', 'status', 'csituacao',
    ];
}
