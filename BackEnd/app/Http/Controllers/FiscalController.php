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

        return response()->json(['message' => "Arquivos enviados com sucesso!"], 201);
    }
}
