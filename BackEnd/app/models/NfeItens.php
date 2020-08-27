<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NfeItens extends Model
{
    protected $table = "nfe_itens";

    protected $fillable = [
        'nfe_id', 'produto_id', 'descricao', 'quantidade', 'valor_unitario', 'desconto', 'total',
        'cfop', 'cst_icms', 'p_icms', 'cst_ipi', 'p_ipi', 'cst_pis', 'p_pis', 'cst_cofins', 'p_cofins',
    ];

    public function produto()
    {
        return $this->belongsTo('App\Models\Product', 'produto_id');
    }
}
