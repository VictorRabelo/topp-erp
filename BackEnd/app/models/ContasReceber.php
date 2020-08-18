<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContasReceber extends Model
{
    protected $table = "contas_receber";

    protected $fillable = [
        'empresa_id', 'categoria_id', 'cliente_id', 'cliente', 'descricao', 'documento', 'valor', 'vencimento',
        'historico', 'situacao', 'juros', 'desconto', 'acrescimo', 'valor_pago', 'data_pago',
    ];
}
