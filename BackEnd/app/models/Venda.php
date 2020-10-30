<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $table = "vendas";

    protected $fillable = [
        'empresa_id', 'user_id', 'cliente_id', 'cliente', 'cpf', 'subtotal', 'desconto', 'total', 'status',
    ];

    public function itens()
    {
        return $this->belongsTo('App\Models\VendaItens', 'venda_id', 'id');
    }

    public function payments()
    {
        return $this->belongsTo('App\Models\VendasPayments', 'venda_id', 'id');
    }
}
