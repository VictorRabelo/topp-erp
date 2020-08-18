<?php

namespace App\Http\Controllers;

use App\Repositories\EmpresaRepositorie;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    function __construct(EmpresaRepositorie $repositorie)
    {
        $this->repo = $repositorie;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->listAll($params);

        return response()->json($resp);
    }

    public function create(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->create($params);

        if (isset($resp['erro'])) {
            return response($resp['erro'], 500);
        }

        return response()->json(['message' => "Cadastro realizado com sucesso!"], 201);
    }

    public function show(int $id)
    {
        $resp = $this->repo->getById($id);

        return response()->json($resp);
    }

    public function update(Request $request, int $id)
    {
        $params = $request->all();
        $resp = $this->repo->update($params, $id);

        if (isset($resp['erro'])) {
            return response($resp['erro'], 500);
        }

        return response()->json(['message' => "Cadastro atualizado com sucesso!"], 201);
    }

    public function destroy(int $id)
    {
        $resp = $this->repo->delete($id);
        if (isset($resp['erro'])) {
            return response($resp['erro'], 500);
        }

        return response()->json($resp);
    }


    public function getUsers(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->getUsers($params);

        return response()->json($resp);
    }

    public function createUser(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->createUser($params);

        if (isset($resp['erro'])) {
            return response($resp['erro'], 500);
        }

        return response()->json(['message' => "Cadastro realizado com sucesso!"], 201);
    }

    public function showUser(int $id)
    {
        $resp = $this->repo->getByIdUser($id);

        return response()->json($resp);
    }

    public function updateUser(Request $request, int $id)
    {
        $params = $request->all();
        $resp = $this->repo->updateUser($params, $id);

        if (isset($resp['erro'])) {
            return response($resp['erro'], 500);
        }

        return response()->json(['message' => "Cadastro atualizado com sucesso!"], 201);
    }

    public function destroyUser(int $id)
    {
        $resp = $this->repo->deleteUser($id);
        if (isset($resp['erro'])) {
            return response($resp['erro'], 500);
        }

        return response()->json($resp);
    }
}
