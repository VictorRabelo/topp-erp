<?php

namespace App\Repositories;

use App\models\EstoqueEntrada;
use App\models\Product;
// use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductRepositorie
{
   function __construct()
   {
      $this->model = new Product();
      $this->user = Auth::guard('api')->user();
      // $this->hashids = new Hashids();
   }

   public function list($params)
   {
      $query = $this->model->where('empresa_id', $this->user->empresa_id)->get();

      $query = $this->parse_dados($query);

      return $query;
   }
   public function parse_dados($list)
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
      // print_r($post);
      $dados_padrao = $this->post_padrao($post);

      $data = $this->model->create($dados_padrao);
      // print_r($data);
      $produto_id = (isset($data->id)) ? $data->id : 0;

      if ($produto_id > 0 && $dados_padrao['estoque'] > 0) {
         $this->entra_estoque($produto_id, $dados_padrao['preco'], $dados_padrao['estoque']);
      }

      return $data;
   }

   public function getSingle(int $id)
   {
      $post = $this->model->find($id);

      $dados_padrao = $this->return_padrao($post);

      return $dados_padrao;
   }

   public function editar($post, int $id)
   {
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

   private function return_padrao(object $dados)
   {
      $dados_return = [
         'id'           => $dados->id,
         'empresa_id' => $dados->empresa_id,
         'codigo_barras' => $dados->codigo_barras,
         'referencia' => $dados->referencia,
         'descricao' => $dados->descricao,
         'custo' => $dados->custo,
         'margem' => $dados->margem,
         'preco' => $dados->preco,
         'estoque' => $dados->estoque,
         'medida' => $dados->medida,
         'origin' => $dados->origin,
         'ncm' => $dados->ncm,
         'cfop' => $dados->cfop,
         'cst' => $dados->cst,
         'foto_atual' => $dados->foto,
         'foto_url' => $this->set_foto($dados->foto),
      ];

      return $dados_return;
   }
   private function post_padrao(array $post)
   {
      $dados_padrao = [
         'empresa_id'      => $this->user->empresa_id,
         'codigo_barras'   => (isset($post['codigo_barras'])) ? $post['codigo_barras'] : null,
         'referencia'      => (isset($post['referencia'])) ? $post['referencia'] : null,
         'descricao'       => (isset($post['descricao'])) ? $post['descricao'] : null,
         'custo'           => (isset($post['custo'])) ? $post['custo'] : 0,
         'margem'          => (isset($post['margem'])) ? $post['margem'] : 0,
         'preco'           => (isset($post['preco'])) ? $post['preco'] : 0,
         'estoque'         => (isset($post['estoque'])) ? $post['estoque'] : 0,
         'medida'          => (isset($post['medida'])) ? $post['medida'] : 'UN',
         'origin'          => (isset($post['origin'])) ? $post['origin'] : 0,
         'ncm'             => (isset($post['ncm'])) ? $post['ncm'] : null,
         'cfop'            => (isset($post['cfop'])) ? $post['cfop'] : null,
         'cst'             => (isset($post['cst'])) ? $post['cst'] : null,
      ];

      if (isset($post['foto']) && $post['foto'] != "") {
         $dados_padrao['foto'] = $this->parse_foto($post);
      }

      return $dados_padrao;
   }


   //movimento do estoque
   private function entra_estoque(int $produto_id, $valor, $quantidade, $nota = null)
   {
      $entrada = new EstoqueEntrada();
      $entrada->produto_id = $produto_id;
      $entrada->valor_unitario = $valor;
      $entrada->quantidade = $quantidade;
      $entrada->nota = $nota;
      return $entrada->save();
   }



   // utilidades
   private function parse_foto($data)
   {
      if (isset($data['foto']) && $data['foto'] != '') {

         $this->_deletePhotoIfExists($data);

         $content = base64_decode($data['foto'][0]);
         $file = fopen('php://temp', 'r+');
         fwrite($file, $content);
         $photo_name = md5(
            uniqid(
               microtime(),
               true
            )
         ) . '.' . pathinfo($data['photo_name'], PATHINFO_EXTENSION);

         Storage::disk('public')
            ->put('fotos/' . $photo_name, $file);
         return $photo_name;
      }
   }
   private function _deletePhotoIfExists(array $data): void
   {
      if (array_key_exists('foto_atual', $data) && $data['foto_atual'] != null) {
         Storage::disk('public')
            ->delete('fotos/' . $data['foto_atual']);
      }
   }
   private function set_foto($foto)
   {
      return Storage::url('fotos/' . $foto);
   }
}
