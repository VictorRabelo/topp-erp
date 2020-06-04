<?php

namespace App\Http\Controllers;

use App\Repositories\VendaItensRepositorie;
use Illuminate\Http\Request;

class VendaItensController extends Controller
{
   function __construct(VendaItensRepositorie $repositorie)
   {
      $this->repo = $repositorie;
   }

   public function index(int $id_venda)
   {
      $resp = $this->repo->list($id_venda);
      return response()->json($resp);
   }

   public function create(Request $request)
   {
      $data = $request->only('venda_id', 'produto_id', 'descricao', 'quantidade', 'valor_unitario', 'desconto', 'total');
      $resp = $this->repo->create_item($data);
      return response()->json($resp);
   }
   public function show(int $id)
   {
      //
   }
   public function update(Request $request, int $id)
   {
      //
   }

   public function destroy(int $id)
   {
      //
   }
}
