<?php

namespace App\Http\Controllers;

use App\Repositories\FiscalRepositorie;
use Illuminate\Http\Request;

class FiscalController extends Controller
{
    function __construct(FiscalRepositorie $repository)
    {
        $this->repo = $repository;
    }

    public function index(Request $request)
    {
        $resp = $this->repo->getMeses($request->all());
        return response()->json($resp);
    }

    public function sendXML(Request $request)
    {
        $data = (object) $request->all();
        $resp = $this->repo->envioXMLContail($data);

        if (isset($resp['errors'])) {
            foreach ($resp['errors'] as $erro) {
                echo "$erro </br></br>";
            }
        } else {
            return response()->json(['message' => "Arquivos enviados com sucesso!"], 201);
        }
    }

    //manifesto
    public function getListaNotas(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->getListaNotas($params);
        return response()->json($resp);
    }

    public function getMonitorSefaz(Request $request)
    {
        $params = $request->all();
        $resp = $this->repo->getMonitorSefaz($params);

        if (isset($resp['error'])) {
            return response($resp, 500);
        }

        return response()->json($resp);
    }

    public function manifestaNFe(Request $request, int $id)
    {
        $params = $request->all();
        $resp = $this->repo->manifestaNFe($params, $id);
        if (isset($resp['errors'])) {
            foreach ($resp['errors'] as $erro) {
                echo "{$erro} <br>";
            }
        } else {
            return response()->json($resp);
        }
    }
}
