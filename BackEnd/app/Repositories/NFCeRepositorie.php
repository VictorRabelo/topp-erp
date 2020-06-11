<?php

namespace App\Repositories;

use App\models\NFCe;
use App\models\Payment;
use App\models\VendaItens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NFCeRepositorie
{
   function __construct()
   {
      $this->model = new NFCe();
      $this->user = Auth::guard('api')->user();
      // $this->hashids = new Hashids();
   }

   public function list($params)
   {
      $query = $this->model->where('empresa_id', $this->user->empresa_id)->get();

      return $query;
   }


   public function emitir($data)
   {
      $venda_id = $data['id'];

      // $buss

      $itens = VendaItens::with('produto')->where('venda_id', $venda_id)->get();

      $payments = DB::table('vendas_payments')->where('venda_id', $venda_id)->get();


      // $post['empresa_id'] = $this->user->empresa_id;

      // $data = $this->model->create($post);

      return $payments;
   }

   public function getSingle(int $id)
   {
      $dados = $this->model->find($id);

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
}
