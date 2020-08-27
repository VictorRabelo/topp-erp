<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorFiscalEventos extends Model
{
    protected $table = "monitor_fiscal_eventos";

    protected $fillable = [
        'emitente_id', 'sequencia', 'tpevento', 'evento', 'xjust', 'chave', 'nprot', 'cstatus', 'status',
    ];
}
