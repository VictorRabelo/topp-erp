<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Emitente extends Model
{
   protected $table = "emitentes";

   protected $fillable = [
      'empresa_id', 'tipo', 'crt', 'razao', 'fantasia', 'cnpj', 'inscricao_estadual', 'inscricao_municipal', 'telefone',
      'celular', 'email', 'cep', 'logradouro', 'numero', 'bairro', 'complemento', 'cidade', 'ibge', 'uf',
   ];
}
