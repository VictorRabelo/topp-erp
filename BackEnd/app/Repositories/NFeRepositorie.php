<?php

namespace App\Repositories;

use App\Models\NFe;
use App\Models\Emitente;
use App\Models\EmitenteConfig;
use App\Models\NfeItens;
use App\Models\NfePayment;
use App\Models\Product;
use App\Tools\NFe\NFeXML;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NFeRepositorie
{
    function __construct()
    {
        $this->model = new NFe();
        $this->user = Auth::guard('api')->user();
        $this->nfe = new NFeXML();
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

    public function create($data)
    {
        $data['empresa_id'] = $this->user->empresa_id;
        $data['cstatus'] = 1;
        $data['status'] = "Aberta";
        $nfe = $this->model->create($data);
        $nfe->save();

        return $nfe->id;
    }

    public function editar($post, int $id)
    {
        unset($post['resto']);
        $nfc = $this->model->find($id);
        $nfc->fill($post);
        $nfc->save();

        return $nfc;
    }


    //itens
    public function create_item($data)
    {
        $item = NfeItens::create($data);
        $item->save();

        return $item->id;
    }
    public function editar_item($data, $id)
    {
        $item = NfeItens::find($id);
        $item->fill($data);
        $item->save();

        return $item->id;
    }

    public function deleta_item($id)
    {
        $item = NfeItens::find($id);
        return $item->delete();
    }

    public function list_itens($params)
    {
        $nfe_id = $params['nfe_id'];
        $itens = NfeItens::where('nfe_id', $nfe_id)->get();

        //verifica se tem foto em cada item
        for ($i = 0; $i < count($itens); $i++) {
            $produto = Product::find($itens[$i]->produto_id);

            if (!empty($produto->foto)) {
                $itens[$i]->foto = Storage::url('fotos/' . $produto->foto);
            }
        }

        $totais = $this->getTotalItens($itens);

        $payments = $this->getPayments($nfe_id);

        return array('itens' => $itens, 'totais' => $totais, 'payments' => $payments);
    }
    function getTotalItens($itens)
    {
        $descontos = 0;
        $subtotal = 0;
        $total = 0;

        foreach ($itens as $item) {
            $descontos += $item->desconto;
            $subtotal += $item->valor_unitario * $item->quantidade;
            $total += $item->total;
        }

        return array('descontos' => $descontos, 'subtotal' => $subtotal, 'total' => $total);
    }
    function getPayments($id)
    {
        $query = DB::table('nfe_payments')->where('nfe_id', $id)->get();
        return $query;
    }

    //pagamentos
    public function create_payment($data)
    {
        $payment = NfePayment::create($data);
        $payment->save();

        return $payment->id;
    }
    public function deleta_payment($id)
    {
        $payment = NfePayment::find($id);
        return $payment->delete();
    }

    //funções nota
    public function emitir($data)
    {
        // return $data;
        //dados do emitente
        $emitente_id = $data['emitente_id'];
        $emitente = Emitente::find($emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $emitente_id)->where('modelo', 55)->first();

        //dados da nota
        $dados = NFe::find($data['id']);

        //dados dos itens
        $itens = NfeItens::with('produto')->where('nfe_id', $data['id'])->get();

        //dados dos pagamentos
        $payments = NfePayment::with('payment')->where('nfe_id', $data['id'])->get();

        $nfe = new NFeXML($emitente, $dados, $itens, $payments);

        $resp = $nfe->make();

        if (isset($resp['cstatus']) && $resp['cstatus'] == 100) {
            $dados->fill($resp);
            $dados->save();

            if ($resp['tpamb'] == 1) {
                $emitente->config->seq += 1;
            } else {
                $emitente->config->seqHomolog += 1;
            }

            //atualiza a sequencia da nota
            $emitente->config->save();

            //retorna url do PDF
            //  return $this->printNota($data['id']);
        }

        return $resp;
    }

    public function cancelar_nota($data)
    {
        //dados do emitente
        $emitente_id = $data['emitente_id'];
        $emitente = Emitente::find($emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $emitente_id)->where('modelo', 55)->first();

        //dados da nota
        $dados = NFe::find($data['id']);
        $dados->xjust = $data['xjust'];

        $nfe = new NFeXML($emitente, $dados);

        $resp = $nfe->cancelarNFe($dados);

        // return $resp;

        if (isset($resp->cstatus) && ($resp->cstatus == "101" || $resp->cstatus == "135" || $resp->cstatus == "155" || $resp->cstatus == "573")) {
            return $this->consultaNota($resp, $emitente);
        }

        return $resp;
    }

    function consultaNota($nota, $emitente)
    {
        $nfe = new NFeXML($emitente, $nota);
        $consulta = $nfe->consultarChave($nota);

        if (isset($consulta->cstatus)) {
            return $nota =  $consulta->save();
        }

        return $consulta;
    }

    public function printNota(int $id)
    {
        $dados = $this->model->find($id);
        $emitente = Emitente::find($dados->emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $dados->emitente_id)->where('modelo', 55)->first();
        // $dados->cnpj = $emitente->cnpj;
        $nfe = new NFeXML($emitente, $dados);

        $urlPDF = $nfe->getPDF($dados);
        // if ($urlPDF == false) {
        //    return array('pdf_url' => $this->nfce->geraPDF($nota));
        // }

        return array('pdf_url' => $urlPDF);
    }
}
