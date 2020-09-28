<?php

namespace App\Repositories;

use App\Mail\NewUserNotification;
use App\Mail\sendXMLs;
use App\Models\ContasPagar;
use App\Models\Emitente;
use App\Models\EmitenteConfig;
use App\Models\EstoqueEntrada;
use App\Models\MonitorFiscal;
use App\Models\MonitorFiscalEventos;
use App\Models\NFCe;
use App\Models\NFe;
use App\Models\NFeImport;
use App\Models\Product;
use App\Tools\NFe\NFeXML;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class FiscalRepositorie
{

    function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function getMeses($params)
    {
        // if ($params['modelo'] == "55") {
        //     $query = DB::table('nfe')->select("created_at as mes")
        //         ->where('emitente_id', $params['emitente_id'])->orderBy('mes', 'desc')
        //         ->groupBy(DB::raw('MONTH(mes)'))
        //         ->groupBy(DB::raw('YEAR(mes)'));
        // } elseif ($params['modelo'] == "65") {
        //     $query = DB::table('nfce')->select("created_at as mes")
        //         ->where('emitente_id', $params['emitente_id'])->orderBy('mes', 'desc')
        //         ->groupBy(DB::raw('MONTH(mes)'))
        //         ->groupBy(DB::raw('YEAR(mes)'));
        // } else {
        //     $queryNFe = DB::table("nfe")->select("nfe.created_at as mes")
        //         ->where('emitente_id', $params['emitente_id'])
        //         ->orWhere('cstatus', 100)->orWhere('cstatus', 101)->orWhere('cstatus', 135)->orWhere('cstatus', 155)
        //         ->get();

        //     $queryNFCe = DB::table("nfce")->select("nfe.created_at as mes")
        //         ->where('emitente_id', $params['emitente_id'])
        //         ->orWhere('cstatus', 100)->orWhere('cstatus', 101)->orWhere('cstatus', 135)->orWhere('cstatus', 155)
        //         ->get();

        //     if (count($queryNFe) >= count($queryNFCe)) {
        //         $query = DB::table('nfe')
        //             ->select("created_at as mes")
        //             ->leftJoin('nfce', 'nfce.emitente_id', '=', 'nfe.emitente_id')
        //             ->where('emitente_id', $params['emitente_id'])->orderBy('mes', 'desc')
        //             ->groupBy(DB::raw('MONTH(mes)'))
        //             ->groupBy(DB::raw('YEAR(mes)'));
        //     }
        // }

        // $query = $query
        //     ->orWhere('cstatus', 100)->orWhere('cstatus', 101)->orWhere('cstatus', 135)->orWhere('cstatus', 155)
        //     ->toSql();

        $dateStart = date('2020-05-01');
        $query = array();
        for ($i = 1; $i <= 12; $i++) {

            $mes = ['mes' => date('m/Y', strtotime("+{$i} months", strtotime($dateStart)))];

            array_push($query, $mes);
        }

        return $query;
    }

    public function newUserNotification($params)
    {
        // Mail::to('franciscoalex2407@gmail.com')->send(new NewUserNotification());
        Mail::to('franciscoalex2407@gmail.com')->send(new NewUserNotification());
    }

    public function envioXMLContail(object $params)
    {
        $mes = explode('/', $params->mes);
        $mes = $mes[1] . '-' . $mes[0];
        $params->mes = $mes;

        if (empty($params->emailSend)) {
            return array('errors' => ["Informe o Email da contabilidade"]);
        }

        if ($params->modelo == "55") {
            $notas = NFe::where('emitente_id', $params->emitente_id)->where('updated_at', 'like', '%' . $mes . '%')
                ->orWhere('cstatus', 100)->orWhere('cstatus', 101)->orWhere('cstatus', 135)->orWhere('cstatus', 155)
                ->get();
        } elseif ($params->modelo == "65") {
            $notas = NFCe::where('emitente_id', $params->emitente_id)->where('updated_at', 'like', '%' . $mes . '%')
                ->orWhere('cstatus', 100)->orWhere('cstatus', 101)->orWhere('cstatus', 135)->orWhere('cstatus', 155)
                ->get();
        } else {
            $nfe = NFe::where('emitente_id', $params->emitente_id)->where('updated_at', 'like', '%' . $mes . '%')
                ->orWhere('cstatus', 100)->orWhere('cstatus', 101)->orWhere('cstatus', 135)->orWhere('cstatus', 155)
                ->get();
            $nfce = NFCe::where('emitente_id', $params->emitente_id)->where('updated_at', 'like', '%' . $mes . '%')
                ->orWhere('cstatus', 100)->orWhere('cstatus', 101)->orWhere('cstatus', 135)->orWhere('cstatus', 155)
                ->get();

            $notas = array();
            foreach ($nfe as $item) {
                array_push($notas, $item);
            }
            foreach ($nfce as $item) {
                array_push($notas, $item);
            }
        }

        $zip = $this->geraZIP($notas, $params);
        if (is_array($zip)) {
            return $zip;
        }

        $params->file = $zip;

        $send = Mail::to($params->emailSend)
            ->cc(['franciscoalex2407@gmail.com', 'toppstylo@gmail.com'])
            ->send(new sendXMLs($params));

        return $send;
    }

    private function geraZIP($notas, $params)
    {
        $app_public = "app/public/";

        $path_zip = storage_path("{$app_public}" . md5($params->empresa_id) . "/zips/{$params->cnpj}");
        if ($params->modelo == "55") {
            $zipFileName = "XMLs_NFe_{$params->cnpj}_{$params->mes}.zip";
        } elseif ($params->modelo == "65") {
            $zipFileName = "XMLs_NFCe_{$params->cnpj}_{$params->mes}.zip";
        } else {
            $zipFileName = "XMLs_NFe_NFCe_{$params->cnpj}_{$params->mes}.zip";
        }

        $zipFileUrl = $path_zip . '/' . $zipFileName;

        if (!is_dir($path_zip)) {
            mkdir($path_zip, 0777, true);
        }

        $zip = new ZipArchive;
        if (!file_exists($zipFileUrl)) {
            $res = $zip->open($zipFileUrl, ZipArchive::CREATE);
        } else {
            $res = $zip->open($zipFileUrl);
        }
        if ($res === TRUE) {

            foreach ($notas as $item) {
                $mesPath = date('Y-m', strtotime($item->created_at));
                $tpambPath = ($item->tpamb == 1) ? "producao" : "homologacao";
                $locationPath = ($item->cstatus == 100) ? "autorizados" : "cancelados";

                if ($params->modelo == "55") {
                    $pathAutorizados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfe/{$tpambPath}/{$mesPath}/autorizados";
                    $pathCancelados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfe/{$tpambPath}/{$mesPath}/cancelados";

                    if ($item->cstatus == 100) {
                        $path_locale = storage_path("{$app_public}{$pathAutorizados}/{$item->chave}.xml");
                    } else {
                        $path_locale = storage_path("{$app_public}{$pathCancelados}/{$item->chave}.xml");
                    }
                    if (!file_exists($path_locale)) {
                        continue;
                    }
                } elseif ($params->modelo == "65") {
                    $pathAutorizados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfce/{$tpambPath}/{$mesPath}/autorizados";
                    $pathCancelados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfce/{$tpambPath}/{$mesPath}/cancelados";

                    if ($item->cstatus == 100) {
                        $path_locale = storage_path("{$app_public}{$pathAutorizados}/{$item->chave}.xml");
                    } else {
                        $path_locale = storage_path("{$app_public}{$pathCancelados}/{$item->chave}.xml");
                    }
                    if (!file_exists($path_locale)) {
                        continue;
                    }
                } else {

                    $pathAutorizados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfe/{$tpambPath}/{$mesPath}/autorizados";
                    $pathCancelados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfe/{$tpambPath}/{$mesPath}/cancelados";

                    if ($item->cstatus == 100) {
                        $path_locale = storage_path("{$app_public}{$pathAutorizados}/{$item->chave}.xml");
                    } else {
                        $path_locale = storage_path("{$app_public}{$pathCancelados}/{$item->chave}.xml");
                    }

                    if (!file_exists($path_locale)) {
                        $pathAutorizados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfce/{$tpambPath}/{$mesPath}/autorizados";
                        $pathCancelados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfce/{$tpambPath}/{$mesPath}/cancelados";

                        if ($item->cstatus == 100) {
                            $path_locale = storage_path("{$app_public}{$pathAutorizados}/{$item->chave}.xml");
                        } else {
                            $path_locale = storage_path("{$app_public}{$pathCancelados}/{$item->chave}.xml");
                        }
                        if (!file_exists($path_locale)) {
                            continue;
                        }
                    }
                }

                $zip->addFile($path_locale, "{$item->chave}.xml");
            }
            // return $notas;

            $zip->close();
            return $zipFileUrl;
        } else {
            return array('errors' => ["Falha ao gerar Arquivo ZIP"]);
        }
    }

    //manifesto
    public function getListaNotas($params)
    {
        $emitente_id = $params['emitente_id'];
        $emitente = Emitente::find($emitente_id);
        // $emitente->config = EmitenteConfig::where('emitente_id', $emitente_id)->where('modelo', 55)->first();

        $monitor = MonitorFiscal::where('emitente_id', $emitente_id)->orderBy('created_at', 'desc')->get();

        $pathRoot = md5($this->user->empresa_id);

        foreach ($monitor as $item) {
            $exist = Storage::disk('public')->exists("{$pathRoot}/xml/{$emitente->cnpj}/monitor/notas/{$item->chave}.xml");
            $item->pathXML = $exist;

            $item->eventos = MonitorFiscalEventos::where('chave', $item->chave)->where('emitente_id', $item->emitente_id)->get();

            $item->import = NFeImport::where('chave', 'like', $item->chave)->where('empresa_id', $this->user->empresa_id)->first();
        }

        return $monitor;
    }

    public function getMonitorSefaz($params)
    {
        $emitente_id = $params['emitente_id'];
        $emitente = Emitente::find($emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $emitente_id)->where('modelo', 55)->first();

        $utimoNSU = MonitorFiscal::where('emitente_id', $emitente_id)->orderBy('nsu', 'desc')->first();
        $nsu = (isset($utimoNSU->nsu) && $utimoNSU->nsu > 0) ? $utimoNSU->nsu : 0;

        // return $monitor;
        $nfe = new NFeXML($emitente);

        $resp = $nfe->monitor_fiscal($nsu);

        if (isset($resp['error'])) {
            return $resp;
        }

        foreach ($resp as $dados) {

            $reg = MonitorFiscal::where('emitente_id', $emitente_id)->where('chave', $dados['chave'])->first();

            if (!empty($reg)) { //update
                $reg->fill($dados)->save();
            } else {
                $dados['emitente_id'] = $emitente_id;
                MonitorFiscal::create($dados)->save();
            }
        }

        return array('registros' => count($resp));
    }

    public function manifestaNFe($params, $id)
    {
        $emitente_id = $params['emitente_id'];
        $emitente = Emitente::find($emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $emitente_id)->where('modelo', 55)->first();

        $nota = MonitorFiscal::find($id);
        $eventos = MonitorFiscalEventos::where('chave', $nota->chave)->where('emitente_id', $nota->emitente_id)
            ->orderBy('sequencia', 'desc')->first();


        $params['sequencia'] = (isset($eventos->sequencia) && $eventos->sequencia > 0) ? $eventos->sequencia : 1;
        $params['chave'] = $nota->chave;

        // return $monitor;
        $nfe = new NFeXML($emitente);

        $resp = $nfe->manifestarNFe($params);

        if (isset($resp['errors'])) {
            return $resp;
        }

        MonitorFiscalEventos::create($resp);

        return $resp;
    }

    //importação de xml

    public function getDadosXML($params)
    {
        $emitente_id = $params['emitente'];
        $chave = $params['chave'];

        $verify = NFeImport::where('chave', 'like', $chave)->where('empresa_id', $this->user->empresa_id)->get();
        if (count($verify) > 0) {
            return array('error' => "Esta nota ja foi importada!");
        }

        $emitente = Emitente::where('id', $emitente_id)->where('empresa_id', $this->user->empresa_id)->first();

        $pathRoot = md5($this->user->empresa_id);

        $localXML = "{$pathRoot}/xml/{$emitente->cnpj}/monitor/notas/{$chave}.xml";

        $dados = $this->extractXML($localXML);

        return $dados;
    }

    private function extractXML($pathXML)
    {
        $xml = Storage::disk('public')->get($pathXML);

        $dados = simplexml_load_string($xml);

        $nfe = $dados->NFe->infNFe;

        $return['ide'] = [
            'numero_nfe' => (string) $nfe->ide->nNF,
            'data_emissao' => (string) date('Y-m-d', strtotime($nfe->ide->dhEmi)),
            'chave' => (string) $dados->protNFe->infProt->chNFe,
        ];

        $return['fornecedor'] = [
            'razao' => (string) $nfe->emit->xNome,
            'fantasia' => (string) $nfe->emit->xFant,
            'cnpj' => (string) $nfe->emit->CNPJ,
        ];


        $return['itens'] = $this->getItems($nfe->det);

        $return['fatura'] = isset($nfe->cobr) ? $this->getFaturas($nfe->cobr, $return) : [];

        // return $dados;
        return $return;
    }

    private function getItems($det)
    {
        $itens = array();
        foreach ($det as $item) {

            $produto = [
                'empresa_id' => $this->user->empresa_id,
                'codigo_barras' => (string) ($item->prod->cEAN == "SEM GTIN") ? null : (string) $item->prod->cEAN,
                'descricao' => (string) $item->prod->xProd,
                'referencia' => (string) $item->prod->cProd,
                'custo' => (string) $item->prod->vUnCom,
                'margem' => 0,
                'preco' => (string) $item->prod->vUnCom,
                'quantidade' => (string) $item->prod->qCom,
                'medida' => (string) $item->prod->uCom,
                'ncm' => (string) $item->prod->NCM,
                'cfop' => (string) $item->prod->CFOP,
            ];

            $product = Product::where('empresa_id', $this->user->empresa_id);

            if (!empty($produto['codigo_barras'])) {
                $product = $product->where('codigo_barras', 'like', $produto['codigo_barras']);
            }
            // if (!empty($produto['referencia'])) {
            $product = $product->where('referencia', 'like', $produto['referencia']);
            // }

            $produto['product'] = $product->first();

            //ICMS
            $imposto = $this->getImposto($item);

            $produto['origin'] = $imposto['origin'];
            $produto['cst_icms'] = $imposto['cst_icms'];
            $produto['p_icms'] = $imposto['p_icms'];

            //IPI
            $produto['cst_ipi'] = (string) $imposto['cst_ipi'];
            $produto['p_ipi'] = (string) $imposto['p_ipi'];

            //PIS
            $produto['cst_pis'] = (string) $imposto['cst_pis'];
            $produto['p_pis'] = (string) $imposto['p_pis'];

            //COFINS
            $produto['cst_cofins'] = (string) $imposto['cst_cofins'];
            $produto['p_cofins'] = (string) $imposto['p_cofins'];

            array_push($itens, $produto);
        }

        return $itens;
    }
    private function getImposto($item)
    {
        if (isset($item->imposto->ICMS->ICMS00)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS00->orig;
            $imposto['cst_icms'] = (string) 102;
            $imposto['p_icms'] = 0;
        } elseif (isset($item->imposto->ICMS->ICMS10)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS10->orig;
            $imposto['cst_icms'] = (string) 202;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMS10->pICMSST;
        } elseif (isset($item->imposto->ICMS->ICMS20)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS20->orig;
            $imposto['cst_icms'] = (string) 102;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMS20->pICMS;
        } elseif (isset($item->imposto->ICMS->ICMS30)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS30->orig;
            $imposto['cst_icms'] = (string) 202;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMS30->pICMSST;
        } elseif (isset($item->imposto->ICMS->ICMS40)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS40->orig;
            $imposto['cst_icms'] = (string) 103;
            $imposto['p_icms'] = (string) 0;
        } elseif (isset($item->imposto->ICMS->ICMS50)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS50->orig;
            $imposto['cst_icms'] = (string) 400;
            $imposto['p_icms'] = (string) 0;
        } elseif (isset($item->imposto->ICMS->ICMS51)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS51->orig;
            $imposto['cst_icms'] = (string) 900;
            $imposto['p_icms'] = (string) 0;
        } elseif (isset($item->imposto->ICMS->ICMS60)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS60->orig;
            $imposto['cst_icms'] = (string) 500;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMS60->pST;
        } elseif (isset($item->imposto->ICMS->ICMS70)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS70->orig;
            $imposto['cst_icms'] = (string) 201;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMS70->pICMSST;
        } elseif (isset($item->imposto->ICMS->ICMS90)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMS90->orig;
            $imposto['cst_icms'] = (string) 102;
            $imposto['p_icms'] = (string) 0;
        }
        /////////////////////////////////////////////////
        elseif (isset($item->imposto->ICMS->ICMSSN101)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMSSN101->orig;
            $imposto['cst_icms'] = (string) $item->imposto->ICMS->ICMSSN101->CSOSN;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMSSN101->pCredSN;
        } elseif (isset($item->imposto->ICMS->ICMSSN102)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMSSN102->orig;
            $imposto['cst_icms'] = (string) $item->imposto->ICMS->ICMSSN102->CSOSN;
            $imposto['p_icms'] = 0;
        } elseif (isset($item->imposto->ICMS->ICMSSN201)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMSSN201->orig;
            $imposto['cst_icms'] = (string) $item->imposto->ICMS->ICMSSN201->CSOSN;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMSSN201->pICMSST;
        } elseif (isset($item->imposto->ICMS->ICMSSN202)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMSSN202->orig;
            $imposto['cst_icms'] = (string) $item->imposto->ICMS->ICMSSN202->CSOSN;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMSSN202->pICMSST;
        } elseif (isset($item->imposto->ICMS->ICMSSN500)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMSSN500->orig;
            $imposto['cst_icms'] = (string) $item->imposto->ICMS->ICMSSN500->CSOSN;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMSSN500->pST;
        } elseif (isset($item->imposto->ICMS->ICMSSN900)) {
            $imposto['origin'] = (string) $item->imposto->ICMS->ICMSSN900->orig;
            $imposto['cst_icms'] = (string) $item->imposto->ICMS->ICMSSN900->CSOSN;
            $imposto['p_icms'] = (string) $item->imposto->ICMS->ICMSSN900->pICMSST;
        }


        //IPI
        if (isset($item->imposto->IPI->IPITrib)) {
            $imposto['cst_ipi'] = $item->imposto->IPI->IPITrib->CST;
            $imposto['p_ipi'] = isset($item->imposto->IPI->IPITrib->pIPI) ? $item->imposto->IPI->IPITrib->pIPI : 0;
        } else {
            $imposto['cst_ipi'] = $item->imposto->IPI->IPINT->CST;
            $imposto['p_ipi'] = isset($item->imposto->IPI->IPINT->pIPI) ? $item->imposto->IPI->IPINT->pIPI : 0;
        }

        //PIS
        if (isset($item->imposto->PIS->PISAliq)) {
            $imposto['cst_pis'] = $item->imposto->PIS->PISAliq->CST;
            $imposto['p_pis'] = isset($item->imposto->PIS->PISAliq->pPIS) ? $item->imposto->PIS->PISAliq->pPIS : 0;
        } else {
            $imposto['cst_pis'] = $item->imposto->PIS->PISNT->CST;
            $imposto['p_pis'] = isset($item->imposto->PIS->PISNT->pPIS) ? $item->imposto->PIS->PISNT->pPIS : 0;
        }

        //PIS
        if (isset($item->imposto->COFINS->COFINSAliq)) {
            $imposto['cst_cofins'] = $item->imposto->COFINS->COFINSAliq->CST;
            $imposto['p_cofins'] = isset($item->imposto->COFINS->COFINSAliq->pCOFINS) ? $item->imposto->COFINS->COFINSAliq->pCOFINS : 0;
        } else {
            $imposto['cst_cofins'] = $item->imposto->COFINS->COFINSNT->CST;
            $imposto['p_cofins'] = isset($item->imposto->COFINS->COFINSNT->pCOFINS) ? $item->imposto->COFINS->COFINSNT->pCOFINS : 0;
        }

        return $imposto;
    }
    private function getFaturas($item, $nfe)
    {
        $fatura = [
            'numero_fatura' => (string) $item->fat->nFat,
            'subtotal' => (string) $item->fat->vOrig,
            'desconto' => (string) $item->fat->vDesc,
            'total' => (string) $item->fat->vLiq,
        ];

        $duplicatas = array();
        $i = 0;
        foreach ($item->dup as $duplicata) {
            $i++;
            $array = [
                'empresa_id' => (string) $this->user->empresa_id,
                'cliente' => (string) $nfe['fornecedor']['razao'],
                'descricao' => (string) "Duplicata da nota de Nº:" . $nfe['ide']['numero_nfe'],
                'documento' => (string) $fatura['numero_fatura'] . "/" . $duplicata->nDup,
                'valor' => (string) $duplicata->vDup,
                'vencimento' => (string) $duplicata->dVenc
            ];

            array_push($duplicatas, $array);
        }

        $fatura['duplicatas'] = $duplicatas;

        return $fatura;
    }



    public function importDadosXML($params, $id)
    {
        $fatura = $params['fatura'];
        $itens = $params['itens'];
        $ide = $params['ide'];

        $this->importItens($itens, $ide);

        if (isset($fatura['duplicatas'])) {
            $this->importFatura($fatura, $ide);
        }

        $import = NFeImport::create([
            'empresa_id' => $this->user->empresa_id,
            'chave' => $ide['chave'],
            'numero_nfe' => $ide['numero_nfe'],
        ]);

        return $import->save();
    }

    private function importItens($itens, $ide)
    {
        foreach ($itens as $item) {

            if ($item['product'] != null) {
                $produto = Product::find($item['product']['id']);
                $produto->estoque += floatval($item['quantidade']);

                $produto->custo = floatval($item['custo']);
                $produto->margem = floatval($item['margem']);
                $produto->preco = floatval($item['preco']);
                if ($produto->save()) {
                    EstoqueEntrada::create([
                        'produto_id' => $produto->id,
                        'valor_unitario' => $produto->custo,
                        'quantidade' => $item['quantidade'],
                        'nota' => $ide['numero_nfe']
                    ]);
                }
            } else {
                $produto = Product::create($item);
                $produto->estoque += floatval($item['quantidade']);
                if ($produto->save()) {
                    EstoqueEntrada::create([
                        'produto_id' => $produto->id,
                        'valor_unitario' => $produto->custo,
                        'quantidade' => $item['quantidade'],
                        'nota' => $ide['numero_nfe']
                    ]);
                }
            }
        }
    }
    private function importFatura($fatura, $ide)
    {
        foreach ($fatura['duplicatas'] as $item) {
            $conta = ContasPagar::create($item);

            $conta->save();
        }
    }
}
