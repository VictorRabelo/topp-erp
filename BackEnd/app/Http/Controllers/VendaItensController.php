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

   public function index(int $venda_id)
   {
      $resp = $this->repo->list($venda_id);
      return response()->json($resp);
   }

   public function create(Request $request)
   {
      $data = $request->only('venda_id', 'produto_id', 'descricao', 'quantidade', 'valor_unitario', 'desconto', 'total');
      $resp = $this->repo->create_item($data);
      return response()->json($resp);
   }
   public function update(Request $request, int $item_id)
   {
      $data = $request->only('venda_id', 'produto_id', 'descricao', 'quantidade', 'valor_unitario', 'desconto', 'total');
      $resp = $this->repo->update_item($data, $item_id);
      return response()->json($resp);
   }

   public function destroy(int $item_id)
   {
      $resp = $this->repo->delete_item($item_id);
      return response()->json($resp);
   }
}
