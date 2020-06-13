<?php

namespace App\Http\Controllers;

use App\Repositories\NFCeRepositorie;
use Illuminate\Http\Request;

class NFCeController extends Controller
{
   function __construct(NFCeRepositorie $repositorie)
   {
      $this->repo = $repositorie;
   }

   public function index(Request $request)
   {
      $dados = $this->repo->list($request);

      return response()->json($dados);
   }

   public function create(Request $request)
   {
      $data = $request->all();
      $resp = $this->repo->emitir($data);

      if (is_array($resp)) {
         foreach ($resp as $row) {
            echo "$row </br></br>";
         }
      } else {

         return response()->json(['pdf_url' => $resp], 200);
      }
      // return $resp;
   }

   public function show(int $id)
   {
      $dados = $this->repo->getSingle($id);

      return response()->json($dados);
   }

   public function update(Request $request, int $id)
   {
      $request = $request->all();
      $dados = $this->repo->editar($request, $id);

      if (is_array($dados)) {
         return response()->json($dados, 500);
      }

      return response()->json(['message' => "Cadastro atualizado com sucesso!"], 201);
   }

   public function destroy(int $id)
   {
      $dados = $this->repo->delete($id);

      if (is_array($dados)) {
         return response()->json($dados, 500);
      }

      return response()->json(['message' => "Cadastro deletado com sucesso!"], 201);
   }

   public function print(Request $request)
   {
      $data = (object) $request->all();
      $resp = $this->repo->printNota($data);
      return response()->json($resp);
   }
}
