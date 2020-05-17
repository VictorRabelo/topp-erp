<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   protected $table = "clientes";

   protected $fillable = [
      'empresa_id', 'tipo', 'razao', 'fantasia', 'cnpj', 'inscricao_estadual', 'inscricao_municipal', 'telefone', 'celular',
      'email', 'cep', 'logradouro', 'numero', 'bairro', 'complemento', 'cidade', 'ibge', 'uf',
   ];
}
