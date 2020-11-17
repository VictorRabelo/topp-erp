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
        $data = $request->all();
        $dados = $this->repo->resumoCaixa($data);

        return response()->json($dados);
    }

    public function listPayments(Request $request)
    {
        $data = $request->all();
        $dados = $this->repo->listPayments($data);

        return response()->json($dados);
    }

    public function getByIdPayment(int $id)
    {
        $dados = $this->repo->getByIdPayment($id);

        return response()->json($dados);
    }

    public function createPayment(Request $request)
    {
        $data = $request->all();
        $dados = $this->repo->createPayment($data);

        return response()->json($dados);
    }

    public function updatePayment(Request $request, int $id)
    {
        $data = $request->all();
        $dados = $this->repo->updatePayment($data, $id);

        return response()->json($dados);
    }

    public function deletePayments(int $id)
    {
        $dados = $this->repo->deletePayments($id);

        return response()->json($dados);
    }
}
