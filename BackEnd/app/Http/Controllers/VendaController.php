<?php

namespace App\Http\Controllers;

use App\Repositories\VendaRepositorie;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    function __construct(VendaRepositorie $repositorie)
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
        $request = $request->all();
        $dados = $this->repo->novo($request);

        if (is_array($dados)) {
            return response()->json($dados, 500);
        }

        return response()->json(['message' => "Cadastro realizado com sucesso!", 'dados' => $dados], 200);
    }

    public function show(int $id)
    {
        $dados = $this->repo->getSingle($id);

        if (isset($dados['erro'])) {
            return response()->json($dados, 404);
        }

        return response()->json($dados);
    }

    public function update(Request $request, int $id)
    {
        $request = $request->all();
        $dados = $this->repo->editar($request, $id);

        // return response()->json(['message' => "Venda Finalizada com sucesso!"], 201);
        return response()->json($dados);
    }

    public function destroy(int $id)
    {
        $dados = $this->repo->delete($id);

        if (is_array($dados)) {
            return response()->json($dados, 500);
        }

        return response()->json(['message' => "Cadastro deletado com sucesso!"], 201);
    }

    public function cancel(int $id)
    {
        $dados = $this->repo->cancel($id);

        if (is_array($dados)) {
            return response()->json($dados, 500);
        }

        return response()->json(['message' => "Venda cancelada com sucesso!"], 201);
    }

    public function estorno_venda(int $id)
    {
        $resp = $this->repo->estorno_venda($id);

        if (isset($resp['erro'])) {
            return response()->json($resp['erro'], 500);
        }

        return response()->json(['message' => "Venda estornada com sucesso!"], 201);
    }


    public function setClient(Request $request, int $venda_id)
    {
        $request = $request->only('cliente_id', 'cliente', 'cpf');
        $resp = $this->repo->set_cliente($request, $venda_id);

        return response()->json(['message' => "Cliente adicionado com sucesso!"], 200);
    }


    public function geraNFe(Request $request)
    {
        $data = $request->only('id');
        $resp = $this->repo->geraNFe($data);

        return response()->json($resp, 200);
    }

    public function printSale(int $id)
    {
        $resp = $this->repo->printSale($id);

        return response()->json($resp, 200);
    }
}
