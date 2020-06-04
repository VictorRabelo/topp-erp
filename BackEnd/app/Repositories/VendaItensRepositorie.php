<?php

namespace App\Repositories;

use App\models\VendaItens;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendaItensRepositorie
{
   function __construct()
   {
      $this->model = new VendaItens();
      // $this->user = Auth::guard('api')->user();
   }

   public function list($id_venda)
   {
      $query = $this->model->where('venda_id', $id_venda)->get();

      $totais = $this->getTotalItens($query);

      return array('itens' => $query, 'totais' => $totais);
   }
   function getTotalItens($itens)
   {
      $descontos = 0;
      $subtotal = 0;
      $total = 0;

      foreach ($itens as $item) {
         $descontos += $item->desconto;
         $subtotal += $item->valor_unitario * $item->quantidade;
         $total += $item->total;
      }

      return array('descontos' => $descontos, 'subtotal' => $subtotal, 'total' => $total);
   }

   public function create_item(array $data)
   {
      $query = $this->model->fill($data)->save();
      return $query;
   }
}
