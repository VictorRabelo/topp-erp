<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\VendaItens;
use App\Models\Venda;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VendaItensRepositorie
{
   function __construct()
   {
      $this->model = new VendaItens();
      // $this->user = Auth::guard('api')->user();
   }

   public function list($venda_id)
   {
      $itens = $this->model->where('venda_id', $venda_id)->get();

      //verifica se tem foto em cada item
      for ($i = 0; $i < count($itens); $i++) {
         $produto = Product::find($itens[$i]->produto_id);

         if (!empty($produto->foto)) {
            $itens[$i]->foto = Storage::url('fotos/' . $produto->foto);
         }
      }

      $totais = $this->getTotalItens($itens);

      $payments = $this->getPayments($venda_id);

      return array('itens' => $itens, 'totais' => $totais, 'payments' => $payments);
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
   function getPayments($venda_id)
   {
      $query = DB::table('vendas_payments')->where('venda_id', $venda_id)->get();
      return $query;
   }

   public function create_item(array $data)
   {
      $query = $this->model->fill($data)->save();

      $this->update_total_venda($data['venda_id']);

      return $query;
   }
   public function update_item(array $data, int $item_id)
   {
      $item = $this->model->find($item_id);
      $item->fill($data)->save();

      $this->update_total_venda($data['venda_id']);

      return $item;
   }
   public function delete_item(int $item_id)
   {
      $item = $this->model->find($item_id);
      $venda_id = $item->venda_id;
      $query = $item->delete();

      $this->update_total_venda($venda_id);

      return $query;
   }
   function update_total_venda(int $venda_id)
   {
      $query = $this->model->where('venda_id', $venda_id)->get();

      $subtotal = 0;
      $total = 0;
      foreach ($query as $item) {
         $subtotal += $item->valor_unitario * $item->quantidade;
         $total += $item->total;
      }

      Venda::where('id', $venda_id)->update(['subtotal' => $subtotal, 'total' => $total]);
   }
}
