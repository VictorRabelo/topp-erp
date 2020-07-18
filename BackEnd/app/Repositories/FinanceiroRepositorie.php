<?php

namespace App\Repositories;

use App\models\Venda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceiroRepositorie
{
   function __construct()
   {
      // $this->model = new Product();
      $this->user = Auth::guard('api')->user();
      $this->dateNow = date('Y-m-d');
   }

   public function resumoCaixa($params)
   {
      $this->dateNow = "2020-05-23";
      $payments = DB::table('vendas_payments')
         ->select(
            // DB::raw('SUM(vendas_payments.valor) as total'),
            // DB::raw('SUM(vendas.total) as total_venda'),
            // 'vendas.total as total_venda',
            'vendas_payments.*',
            'payments_forms.more'
         )
         ->join('vendas', 'vendas.id', '=', 'vendas_payments.venda_id')
         ->join('payments_forms', 'payments_forms.id', '=', 'vendas_payments.forma_id')
         ->where('vendas.created_at', 'like', '%' . $this->dateNow . '%')
         // ->groupBy('vendas_payments.forma_id')
         ->get();


      // for ($i = 0; $i < count($vendas); $i++) {
      //    if ($vendas[$i]->more == 1) {
      //       $vendas[$i]->total = ;
      //    }
      // }

      return $payments;

      // foreach ($vendas as $venda) {
      //    # code...
      // }

   }
}
