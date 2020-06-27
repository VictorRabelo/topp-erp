<?php

namespace App\Repositories;

use App\models\Client;
use App\models\EstoqueSaida;
use App\models\NFCe;
use App\models\Product;
use App\models\Venda;
use App\models\VendaItens;
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


   public function novo($post)
   {
      $insert = $this->model;

      $dados['empresa_id'] = $this->user->empresa_id;
      $dados['user_id'] = $this->user->id;

      return $insert->create($dados);
   }

   public function getSingle(int $id)
   {
      $dados = $this->model->where('id', $id)->first();
      $client = Client::find($dados->cliente_id);
      $dados->cliente = (isset($client->razao)) ? $client->razao : "Consumidor Final";
      $vendedor = User::find($dados->user_id);
      $dados->vendedor = (isset($vendedor->nome)) ? $vendedor->nome : "";

      $nfce = NFCe::where('venda_id', $id)->where('cstatus', 100)->orderBy('created_at', 'desc')->first();
      $dados->nfce = $nfce;

      // $dados = $this->return_padrao($dados);

      return $dados;
   }

   public function editar($post, int $id)
   {
      $venda = $post['vendaCurrent'];
      $payments = $post['paymentsCurrent'];

      $_gen_payments = $this->gen_payments($payments, $venda['id']);

      if (!$_gen_payments) {
         return $_gen_payments;
      }

      $_finish_venda = $this->finish_venda($venda);

      return $_finish_venda;
   }

   function finish_venda(array $dados)
   {
      // print_r($dados);
      $venda = $this->model->find($dados['id']);
      $venda->status = 10;
      $venda->desconto = $dados['desconto_b'];
      $venda->total = $dados['total'];

      $this->move_estoque_saida($dados['id']);

      return $venda->save();
   }
   function gen_payments(array $payments, int $venda_id)
   {
      foreach ($payments as $payment) {
         $data = [
            'venda_id' => $venda_id,
            'forma_id' => $payment['id'],
            'forma' => $payment['forma'],
            'valor' => $payment['valor'],
            'obs' => (isset($payment['obs'])) ? $payment['obs'] : null
         ];
         $query = DB::table('vendas_payments')->insertGetId($data);
         if ($query < 1) {
            DB::table('vendas_payments')->where('venda_id', $venda_id)->delete();
            return false;
         }
      }

      return true;
   }
   function move_estoque_saida($venda_id)
   {
      $itens = VendaItens::where('venda_id', $venda_id)->get();

      foreach ($itens as $item) {
         $dados = array(
            'venda_id' => $item['venda_id'],
            'produto_id' => $item['produto_id'],
            'valor_unitario' => $item['valor_unitario'],
            'quantidade' => $item['quantidade']
         );

         $mov = EstoqueSaida::create($dados);

         $produto = Product::find($item['produto_id']);
         $produto->estoque -= $item['quantidade'];
         $produto->save();
      }
   }

   public function delete(int $id)
   {
      $dados = $this->model->find($id);
      return $dados->delete();
   }

   public function set_cliente($post, int $venda_id)
   {
      $venda = $this->model->find($venda_id);
      $resp = $venda->fill($post)->save();

      return $resp;
   }
}
