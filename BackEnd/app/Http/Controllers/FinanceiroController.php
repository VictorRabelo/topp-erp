<?php

namespace App\Http\Controllers;

use App\Repositories\FinanceiroRepositorie;
use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
   function __construct(FinanceiroRepositorie $repositorie)
   {
      $this->repo = $repositorie;
   }

   public function resumoCaixa(Request $request)
   {
      $data = $request->only('data');
      $dados = $this->repo->resumoCaixa($data);

      return response()->json($dados);
   }
}
