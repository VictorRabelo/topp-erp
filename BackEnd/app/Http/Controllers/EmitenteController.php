<?php

namespace App\Http\Controllers;

use App\Models\Emitente;
use App\Repositories\EmitenteRepositorie;
use Illuminate\Http\Request;

class EmitenteController extends Controller
{
    function __construct(EmitenteRepositorie $repositorie)
    {
        $this->repo = $repositorie;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->list($params);

        return response()->json($resp);
    }

    public function create(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->create($params);

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

        return response()->json($resp);
    }

    public function destroy(int $id)
    {
        $resp = $this->repo->delete($id);

        return response()->json($resp);
    }

    public function configDados(int $id)
    {
        $resp = $this->repo->getByIdConfig($id);

        return response()->json($resp);
    }

    public function configCreate(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->createConfig($params);

        return response()->json($resp);
    }

    public function configUpdate(Request $request, int $id)
    {
        $params = $request->all();
        $resp = $this->repo->updateConfig($params, $id);

        return response()->json($resp);
    }

    public function configCertificate(Request $request, int $id)
    {
        $params = $request->only('cnpj', 'file_pfx', 'senha_pfx', 'file', 'file_name');
        $resp = $this->repo->updateCertificate($params, $id);

        return response()->json($resp);
    }
}
