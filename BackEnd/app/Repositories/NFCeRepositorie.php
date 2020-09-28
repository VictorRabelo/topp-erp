<?php

namespace App\Repositories;

use App\Models\NFCe;
use App\Models\Emitente;
use App\Models\EmitenteConfig;
use App\Models\Client;
use App\Models\Venda;
use App\Models\VendaItens;
use App\Tools\NFCe\NFCeXML;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NFCeRepositorie
{
    function __construct()
    {
        $this->model = new NFCe();
        $this->user = Auth::guard('api')->user();
        //   $this->nfce = new NFCeXML();
        // $this->hashids = new Hashids();
    }

    public function list($params)
    {
        $query = $this->model->with(['emitente', 'venda'])->where('empresa_id', $this->user->empresa_id)->get();

        return $query;
    }

    public function getSingle(int $id)
    {
        $dados = $this->model->find($id);

        return $dados;
    }


    public function emitir($data)
    {
        //dados do emitente
        $emitente_id = $data['emitente_id'];
        $emitente = Emitente::find($emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $emitente_id)->where('modelo', 65)->first();

        //dados da venda
        $venda_id = $data['venda_id'];
        $venda = Venda::find($venda_id);

        //dados do cliente
        if ($venda->cliente_id > 0) {
            $venda->client = Client::find($venda->cliente_id);
        } elseif (strlen($venda->cpf) > 0) {
            $venda->client = (object) ['razao' => $venda->cliente, 'cnpj' => $venda->cpf];
        }

        //dados dos itens
        $itens = VendaItens::with('produto')->where('venda_id', $venda_id)->get();

        //dados dos pagamentos
        $payments = DB::table('vendas_payments')->where('venda_id', $venda_id)->get();

        $nfce = new NFCeXML($emitente, null, $venda, $itens, $payments);

        //verifica se ha nota emitida com esses dados
        // $verificaNota = $this->model->where('emitente_id', $emitente->id)
        //     ->where('tpamb', $emitente->config->tpamb)
        //     ->where('venda_id', $venda_id)
        //     ->orderBy('id', 'desc')
        //     ->first();

        // if (!empty($verificaNota)) {
        //     $urlPDF = $nfce->getPDF($verificaNota);
        //     // if ($urlPDF == false) {
        //     //     return $nfce->geraPDF($verificaNota);
        //     // }
        //     return $urlPDF;
        // }

        //gera nfce
        $resp = $nfce->make();

        if (isset($resp['cstatus']) && $resp['cstatus'] == 100) {
            $dados = $this->create($resp);

            if ($resp['tpamb'] == 1) {
                $emitente->config->seq += 1;
            } else {
                $emitente->config->seqHomolog += 1;
            }

            //atualiza a sequencia da nota
            $emitente->config->save();

            //retorna url do PDF
            // return $this->printNota($dados);
        }

        return $resp;
    }

    public function create($data)
    {
        $data['empresa_id'] = $this->user->empresa_id;
        $nfc = $this->model->fill($data);
        $nfc->save();

        return $nfc;
    }

    public function editar($post, int $id)
    {
        $nfc = $this->model->find($id);
        $nfc->fill($post);
        $nfc->save();

        return $nfc;
    }

    public function cancela($data, int $id)
    {
        //dados do emitente
        $emitente_id = $data['emitente_id'];
        $emitente = Emitente::find($emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $emitente_id)->where('modelo', 65)->first();

        //dados da nota
        $dados = NFCe::find($id);
        $dados->xjust = $data['xjust'];

        $nfe = new NFCeXML($emitente, $dados);

        $resp = $nfe->cancelarNFe($dados);

        // return $resp;

        if (isset($resp->cstatus) && ($resp->cstatus == "101" || $resp->cstatus == "135" || $resp->cstatus == "155" || $resp->cstatus == "573")) {
            return $this->consultaNota($resp, $emitente);
        }

        return $resp;
    }
    function consultaNota($nota, $emitente)
    {
        $nfe = new NFCeXML($emitente, $nota);
        $consulta = $nfe->consultarChave($nota);

        if (isset($consulta->cstatus)) {
            return $nota =  $consulta->save();
        }

        return $consulta;
    }

    public function printNota($data)
    {
        $dados = $this->model->find($data->id);
        $emitente = Emitente::find($dados->emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $dados->emitente_id)->where('modelo', 65)->first();
        // $dados->cnpj = $emitente->cnpj;
        $nfe = new NFCeXML($emitente, $dados);

        $urlPDF = $nfe->geraPDF($dados);
        // if ($urlPDF == false) {
        //    return array('pdf_url' => $this->nfce->geraPDF($nota));
        // }

        return array('pdf_url' => $urlPDF);
    }
}
