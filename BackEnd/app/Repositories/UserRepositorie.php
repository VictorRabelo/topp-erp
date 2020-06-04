<?php

namespace App\Repositories;

use App\models\Client;
use App\User;
// use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRepositorie
{
   function __construct()
   {
      $this->model = new User();
      $this->user = Auth::guard('api')->user();
      // $this->hashids = new Hashids();
   }

   public function list($params)
   {
      $query = DB::table("users")
         ->select('users.*', 'users_permissions.descricao as nivel')
         ->leftJoin('users_permissions', 'users_permissions.id', 'users.permissions')
         ->where('users.empresa_id', $this->user->empresa_id)->get();

      return $query;
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
      $model = $this->model->find($id);
      $model->fill($post);

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


   public function lista_permissoes()
   {
      $query = DB::table("users_permissions")
         ->where('empresa_id', $this->user->empresa_id)->get();

      return $query;
   }
   public function getSinglePermissions(int $id)
   {
      $permissions = DB::table('users_permissions')->find($id);

      return $permissions;
   }
   public function nova_permissions($post)
   {
      $post['empresa_id'] = $this->user->empresa_id;
      $query = DB::table('users_permissions')->insertGetId($post);

      return $query;
   }
   public function editar_permissions($post, int $id)
   {
      $post['empresa_id'] = $this->user->empresa_id;
      $query = DB::table('users_permissions')->where('id', $id)->update($post);

      return $query;
   }
}
