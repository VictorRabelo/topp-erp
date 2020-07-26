<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFe extends Model
{
   protected $table = "nfe";

   protected $fillable = [
      'empresa_id', 'emitente_id', 'venda_id', 'cliente_id', 'razao', 'fantasia', 'cnpj', 'inscricao_estadual',
      'logradouro', 'numero', 'bairro', 'complemento', 'cidade', 'uf', 'ibge', 'transportadora_id', 'transportadora', 'transp_cnpj',
      'transp_inscricao_estadual', 'transp_uf', 'transp_quantidade', 'transp_especie', 'transp_pesoBruto', 'transp_pesoLiquido',
      'transp_placa', 'transp_placaUF', 'subtotal', 'frete', 'desconto', 'total', 'ind_pres', 'ind_final', 'mod_frete',
      'tipo_nf', 'finalidade_nf', 'serie', 'sequencia', 'tpamb', 'cstatus', 'status', 'chave', 'chave_referencia', 'nota_referencia',
      'recibo', 'protocolo', 'xjust', 'infor_adicional',
   ];

   public function emitente()
   {
      return $this->belongsTo('App\models\Emitente', 'emitente_id');
   }

   public function venda()
   {
      return $this->belongsTo('App\models\Venda', 'venda_id');
   }
}
