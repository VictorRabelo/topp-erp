<?php

namespace App\Http\Controllers;

use App\Repositories\NFeRepositorie;
use Illuminate\Http\Request;

class NFeController extends Controller
{
    function __construct(NFeRepositorie $repositorie)
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
        $resp = $this->repo->create($data);

        return response()->json(['id' => $resp], 200);
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

        return response()->json(['message' => "Dados atualizado com sucesso!"], 201);
    }

    public function destroy(int $id)
    {
        $resp = $this->repo->deleta($id);
        if (is_array($resp)) {
            return response()->json($resp, 500);
        }

        return response()->json(['message' => "Deletado com sucesso!"], 201);
    }

    public function print(int $id)
    {
        // return storage_path("c4ca4238a0b923820dcc509a6f75849b/fotos/logos/9370f2a83b01631f6bbbb48ada2b98d7.png");
        $resp = $this->repo->printNota($id);
        return response()->json($resp);
    }



    public function index_item(Request $request)
    {
        $dados = $this->repo->list_itens($request);

        return response()->json($dados);
    }

    public function create_item(Request $request)
    {
        $data = $request->only('nfe_id', 'produto_id', 'descricao', 'quantidade', 'valor_unitario', 'desconto', 'total', 'cfop', 'cst_icms', 'p_icms', 'cst_ipi', 'p_ipi', 'cst_pis', 'p_pis', 'cst_cofins', 'p_cofins');
        $resp = $this->repo->create_item($data);

        return response()->json(['message' => "Item adicionado com sucesso!"], 201);
    }

    public function update_item(Request $request, int $id)
    {
        $data = $request->only('nfe_id', 'produto_id', 'descricao', 'quantidade', 'valor_unitario', 'desconto', 'total', 'cfop', 'cst_icms', 'p_icms', 'cst_ipi', 'p_ipi', 'cst_pis', 'p_pis', 'cst_cofins', 'p_cofins');
        $dados = $this->repo->editar_item($data, $id);

        return response()->json(['message' => "Item atualizado com sucesso!"], 201);
    }

    public function destroy_item(int $id)
    {
        $resp = $this->repo->deleta_item($id);
        return response()->json(['message' => "Item removido com sucesso!"], 201);
    }


    public function create_payment(Request $request)
    {
        $data = $request->only('nfe_id', 'forma_id', 'forma', 'valor', 'obs');
        $resp = $this->repo->create_payment($data);

        return response()->json(['message' => "Pagamento adicionado com sucesso!"], 201);
    }

    public function destroy_payment(int $id)
    {
        $resp = $this->repo->deleta_payment($id);
        return response()->json(['message' => "Item removido com sucesso!"], 201);
    }


    public function transmitir(Request $request)
    {
        $data = $request->only('emitente_id', 'id');
        $resp = $this->repo->emitir($data);

        if (isset($resp['erros'])) {
            foreach ($resp['erros'] as $erro) {
                echo "$erro </br></br>";
            }
        } else {
            return response()->json(['message' => $resp['status']], 201);
        }
    }
    public function cancelar(Request $request)
    {
        $data = $request->only('emitente_id', 'id', 'xjust');
        $resp = $this->repo->cancelar_nota($data);

        if (isset($resp['erros'])) {
            foreach ($resp['erros'] as $erro) {
                echo "$erro </br></br>";
            }
        } else {
            return response()->json($resp, 200);
        }
    }


    //referencias
    public function search_references(Request $request)
    {
        $data = $request->only('termo');
        $resp = $this->repo->search_references($data);
        return response()->json($resp);
    }
    public function get_references(int $id)
    {
        $resp = $this->repo->get_references($id);
        return response()->json($resp);
    }
    public function create_references(Request $request)
    {
        $data = $request->only('sequencia', 'chave', 'nfe_id');
        $resp = $this->repo->create_references($data);
        return response()->json($resp);
    }
    public function remove_references(int $id)
    {
        $resp = $this->repo->remove_references($id);
        return response()->json($resp);
    }
}
