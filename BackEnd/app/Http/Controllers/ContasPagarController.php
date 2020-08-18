<?php

namespace App\Http\Controllers;

use App\Repositories\ContasPagarRepositorie;
use Illuminate\Http\Request;

class ContasPagarController extends Controller
{
    function __construct(ContasPagarRepositorie $repositorie)
    {
        $this->repo = $repositorie;
    }

    public function index(Request $request)
    {
        $dados = $this->repo->getList($request);

        return response()->json($dados);
    }

    public function create(Request $request)
    {
        $request = $request->all();
        $resp = $this->repo->create($request);

        if (is_array($resp)) {
            return response()->json($resp, 500);
        }

        return response()->json(['message' => "Conta cadastrada com sucesso!"], 201);
    }

    public function show(int $id)
    {
        $dados = $this->repo->getSingle($id);

        return response()->json($dados);
    }

    public function update(Request $request, int $id)
    {
        $request = $request->all();
        $resp = $this->repo->update($request, $id);

        if (is_array($resp)) {
            return response()->json($resp, 500);
        }

        return response()->json(['message' => "Conta atualizada com sucesso!"], 201);
    }

    public function destroy(int $id)
    {
        $resp = $this->repo->delete($id);

        if (is_array($resp)) {
            return response()->json($resp, 500);
        }

        return response()->json(['message' => "Cadastro deletado com sucesso!"], 201);
    }

    public function paymentConta(Request $request)
    {
      //   return response()->json("", 500);

        $request = $request->all();
        $resp = $this->repo->paymentConta($request);

        if (is_array($resp)) {
            return response()->json($resp, 500);
        }

        return response()->json(['message' => "Conta Paga com sucesso!"], 201);
    }
}
