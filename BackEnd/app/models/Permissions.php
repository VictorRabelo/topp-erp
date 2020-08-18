<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $table = "users_permissions";

    protected $fillable = [
        'empresa_id', 'descricao', 'users', 'create_user', 'edit_user', 'delete_user', 'nivel_user', 'products', 'create_product',
        'edit_product', 'delete_product', 'clients', 'create_client', 'edit_client', 'delete_client', 'vendas', 'create_venda', 'edit_venda',
        'delete_venda', 'cancel_venda', 'finalizar_venda', 'desconto_venda', 'nfe', 'create_nfe', 'edit_nfe', 'delete_nfe', 'nfce', 'create_nfce',
        'edit_nfce', 'delete_nfce', 'emitentes', 'create_emitente', 'edit_emitente', 'delete_emitente', 'caixa', 'contas_receber', 'contas_receber_create',
        'contas_receber_edit', 'contas_receber_delete', 'contas_receber_payment', 'contas_pagar', 'contas_pagar_create', 'contas_pagar_edit',
        'contas_pagar_delete', 'contas_pagar_payment', 'create_caixa', 'edit_caixa', 'delete_caixa',
    ];
}
