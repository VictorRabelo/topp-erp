<?php

namespace App\Repositories;

use App\models\Client;
use App\models\Venda;
use App\User;
// use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendaRepositorie
{
   function __construct()
   {
      $this->model = new Venda();
      $this->user = Auth::guard('api')->user();
      // $this->hashids = new Hashids();
   }

   public function list($params)
   {
      $query = $this->model->where('empresa_id', $this->user->empresa_id)->get();

      // $query = $this->order_list($query);

      return $query;
   }
   public function order_list($list)
   {

      $array = array();
      foreach ($list as $post) {

         $dados = $this->return_padrao($post);

         array_push($array, $dados);
      }

      return $array;
   }

   public function novo($post)
   {
      $insert = $this->model;

      $dados['empresa_id'] = $this->user->empresa_id;
      $dados['user_id'] = $this->user->id;

      return $insert->create($dados);
   }

   public function getSingle(int $id)
   {
      $dados = $this->model->find($id);
      $client = Client::find($dados->cliente_id);
      $dados->cliente = (isset($client->nome)) ? $client->nome : "Consumidor Final";
      $vendedor = User::find($dados->user_id);
      $dados->vendedor = (isset($vendedor->nome)) ? $vendedor->nome : "";

      // $dados = $this->return_padrao($dados);

      return $dados;
   }

   public function editar($post, int $id)
   {
      if (isset($post['cnpj'])) {
         $verifica = $this->verifyCNPJ($post['cnpj'], $id);
         if (count($verifica) > 0) {
            return ['message' => "CNPJ/CPF: " . $post['cnpj'] . " vinculado a outro cadastro!"];
         }
      }

      $dados_padrao = $this->post_padrao($post);

      $model = $this->model->find($id);
      $model->fill($dados_padrao);

      return $model->save();
   }

   public function delete(int $id)
   {
      $dados = $this->model->find($id);
      return $dados->delete();
   }

   function verifyCNPJ($cnpj, int $id = null)
   {

      if (empty($cnpj)) {
         return [];
      }

      $query = DB::table('clientes')->where('cnpj', $cnpj)->where('empresa_id', $this->user->empresa_id);

      if ($id > 0) {
         $query = $query->where('id', '!=', $id);
      }

      // print_r($query->toSql());
      return $query->get();
   }

   function return_padrao(object $dados)
   {
      $dados_return = [
         'id'           => $dados->id,
         'tipo'         => $dados->tipo,
         'empresa_id'   => $dados->empresa_id,
         'razao'        => $dados->razao,
         'fantasia'     => $dados->fantasia,
         'cnpj'         => $dados->cnpj,
         'ie'           => $dados->inscricao_estadual,
         'im'           => $dados->inscricao_municipal,
         'telefone'     => $dados->telefone,
         'celular'      => $dados->celular,
         'email'        => $dados->email,
         'cep'          => $dados->cep,
         'logra'        => $dados->logradouro,
         'numero'       => $dados->numero,
         'bairro'       => $dados->bairro,
         'complemento'  => $dados->complemento,
         'cidade'       => $dados->cidade,
         'uf'           => $dados->uf,
         'ibge'         => $dados->ibge,
      ];

      return $dados_return;
   }
   function post_padrao(array $post)
   {
      $dados_padrao = [
         'tipo'                  => (isset($post['tipo'])) ? $post['tipo'] : null,
         'empresa_id'            => $this->user->empresa_id,
         'razao'                 => (isset($post['razao'])) ? $post['razao'] : null,
         'fantasia'              => (isset($post['fantasia'])) ? $post['fantasia'] : null,
         'cnpj'                  => (isset($post['cnpj'])) ? $post['cnpj'] : null,
         'inscricao_estadual'    => (isset($post['ie'])) ? $post['ie'] : null,
         'inscricao_municipal'   => (isset($post['im'])) ? $post['im'] : null,
         'telefone'              => (isset($post['telefone'])) ? $post['telefone'] : null,
         'celular'               => (isset($post['celular'])) ? $post['celular'] : null,
         'email'                 => (isset($post['email'])) ? $post['email'] : null,
         'cep'                   => (isset($post['cep'])) ? $post['cep'] : null,
         'logradouro'            => (isset($post['logra'])) ? $post['logra'] : null,
         'numero'                => (isset($post['numero'])) ? $post['numero'] : null,
         'bairro'                => (isset($post['bairro'])) ? $post['bairro'] : null,
         'complemento'           => (isset($post['complemento'])) ? $post['complemento'] : null,
         'cidade'                => (isset($post['cidade'])) ? $post['cidade'] : null,
         'uf'                    => (isset($post['uf'])) ? $post['uf'] : null,
         'ibge'                  => (isset($post['ibge'])) ? $post['ibge'] : null,
      ];

      return $dados_padrao;
   }
}
