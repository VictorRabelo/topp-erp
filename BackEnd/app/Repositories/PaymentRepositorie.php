<?php

namespace App\Repositories;

use App\Models\Payment;
// use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentRepositorie
{
   function __construct()
   {
      $this->model = new Payment();
      $this->user = Auth::guard('api')->user();
      // $this->hashids = new Hashids();
   }

   public function list($params)
   {
      $query = $this->model->where('empresa_id', $this->user->empresa_id)->get();

      return $query;
   }


   public function novo($post)
   {
      $post['empresa_id'] = $this->user->empresa_id;
      $data = $this->model->create($post);

      return $data;
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
