<?php

namespace App\Tools\NFCe;

use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;
use NFePHP\Common\Keys;
use NFePHP\DA\NFe\Danfce;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;

/**
 * Monta o XML de uma NFCe.
 *
 * @author Guilherme Vilela Oliveira <guivo11@gmail.com>
 */
class NFCeXML
{
    public const VERSION = '4.00';

    public const NATURE_OPERATION = 'VENDA';

    public const OPERATION_INPUT = 0;
    public const OPERATION_EXIT = 1;

    public const INTERN_OPERATION = 1;

    public const ENVIRONMENT_PRODUCTION = 1;
    public const ENVIRONMENT_SANDBOX = 2;

    public const COUNTRY_CODE = '1058';
    public const COUNTRY_NAME = 'BRASIL';

    public const PAY_ICMS = 1;
    public const FREE_ICMS = 9;

    protected $business;
    protected $nota;
    protected $venda;
    protected $items;
    protected $payments;

    protected $xml;

    private $_vBC = 0;

    private $_statesCode = [
        'RO' => 11, 'AC' => 12, 'AM' => 13, 'RR' => 14, 'PA' => 15, 'AP' => 16,
        'TO' => 17, 'MA' => 21, 'PI' => 22, 'CE' => 23, 'RN' => 24, 'PB' => 25,
        'PE' => 26, 'AL' => 27, 'SE' => 28, 'BA' => 29, 'MG' => 31, 'ES' => 32,
        'RJ' => 33, 'SP' => 35, 'PR' => 41, 'SC' => 42, 'RS' => 43, 'MS' => 50,
        'MT' => 51, 'GO' => 52, 'DF' => 53
    ];

    function __construct(object $business = null, object $nota = null, object $venda = null, object $items = null, object $payment = null)
    {
        $this->business = $business;
        $this->nota = $nota;
        $this->venda = $venda;
        $this->items = $items;
        $this->payments = $payment;

        //variaveis global
        if ($business != null) {
            $this->tpamb = $this->business->config->tpamb;
            $this->mesPath = date('Y-m', strtotime(isset($nota->updated_at) ? $nota->updated_at : date('Y-m-d')));
            $this->tpambPath = ($this->tpamb == 1) ? "producao" : "homologacao";
            $this->pathRoot = md5($business->empresa_id);

            $this->pathGerados = "{$this->pathRoot}/xml/{$this->business->cnpj}/nfce/{$this->tpambPath}/{$this->mesPath}/gerados";
            $this->pathAutorizados = "{$this->pathRoot}/xml/{$this->business->cnpj}/nfce/{$this->tpambPath}/{$this->mesPath}/autorizados";
            $this->pathCancelados = "{$this->pathRoot}/xml/{$this->business->cnpj}/nfce/{$this->tpambPath}/{$this->mesPath}/cancelados";
            $this->pathPDF = "{$this->pathRoot}/pdf/{$this->business->cnpj}/nfce/{$this->tpambPath}/{$this->mesPath}";

            $this->numero = ($this->tpamb == 1) ? $this->business->config->seq : $this->business->config->seqHomolog;
            $this->serie = ($this->tpamb == 1) ? $this->business->config->serie : $this->business->config->serieHomolog;

            $this->config  = [
                "atualizacao" => date('Y-m-d h:i:s'),
                "tpAmb" => $this->tpamb,
                "razaosocial" => $business->razao,
                "cnpj" => $business->cnpj, // PRECISA SER VÁLIDO
                "ie" => $business->inscricao_estadual, // PRECISA SER VÁLIDO
                "siglaUF" => $business->uf,
                "schemes" => "PL_009_V4",
                "versao" => self::VERSION,
                "tokenIBPT" => "AAAAAAA",
                "CSC" => ($this->tpamb == 1) ? $business->config->csc : $business->config->cscHomolog,
                "CSCid" => ($this->tpamb == 1) ? $business->config->cscid : $business->config->cscidHomolog,
                "aProxyConf" => [
                    "proxyIp" => "",
                    "proxyPort" => "",
                    "proxyUser" => "",
                    "proxyPass" => ""
                ]
            ];

            $this->configJson = json_encode($this->config);

            $this->certificate = Storage::disk('public')->get("{$this->pathRoot}/certificates/{$business->cnpj}/{$business->file_pfx}");

            $this->tools = new Tools($this->configJson, Certificate::readPfx($this->certificate, $business->senha_pfx));
            $this->tools->model(65);
        }
    }

    /**
     * Constrói o XML.
     *
     * @return mixed
     */
    public function make()
    {
        $this->nfe = new Make();

        //cabeçalho da nota
        $this->gen_IDE();

        //emitente
        $this->get_Emitente();

        //destinatário
        if (isset($this->venda->client)) {
            $this->gen_Destinatario();
        }

        //itens da nota
        $this->gen_itens();

        //transportes
        $this->gen_transporte();

        //pagamentos
        $this->gen_payments();

        //informações adicionais
        // $std = new \stdClass();
        // $std->infCpl = $this->nota->infor_adicional;
        // $this->nfe->taginfAdic($std);

        try {
            $this->xml = $this->nfe->getXml();

            file_put_contents('./nfe.xml', $this->xml);

            Storage::disk('public')->put("{$this->pathGerados}/{$this->chave}.xml", $this->xml);

            return $this->sign_nota();

            return $this->xml;
        } catch (\Exception $e) {
            return ['erros' => [$this->nfe->getErrors()]];
        }
    }

    private function gen_IDE()
    {
        $std = new \stdClass();
        $std->versao = self::VERSION;
        $this->nfe->taginfNFe($std);

        $std = new \stdClass();
        $std->cUF = $this->_getStateCode($this->business->uf);
        $std->cNF = STR_PAD($this->numero + 1, 8, '0', STR_PAD_LEFT);
        $std->natOp = self::NATURE_OPERATION;
        // $std->indPag = $payment;
        $std->mod = 65;
        $std->serie = $this->serie;
        $std->nNF = $this->numero;
        $std->dhEmi = date('Y-m-d\TH:i:sP');
        $std->tpNF = 1;
        $std->idDest = 1;
        $std->cMunFG = $this->business->ibge;
        $std->tpImp = $this->business->config->tpimp;
        $std->tpEmis = $this->business->config->tpemiss;
        // $std->cDV = 0;
        $std->tpAmb = $this->tpamb;
        $std->finNFe = 1;
        $std->indFinal = 1;
        $std->indPres = 1;
        $std->procEmi = 0;
        $std->verProc = 'TOPP-ERP-1.0.0';

        // Monta Chave da NF-e
        $this->chave = Keys::build(
            $std->cUF,
            date('y', strtotime($std->dhEmi)),
            date('m', strtotime($std->dhEmi)),
            $this->business->cnpj,
            $std->mod,
            $std->serie,
            $std->nNF,
            $std->tpEmis,
            $std->cNF
        );

        $std->cDV = substr($this->chave, -1);

        $this->nfe->tagide($std);
    }

    private function get_Emitente()
    {
        $std = new \stdClass();
        $std->xNome = $this->business->razao;
        $std->IE = $this->business->inscricao_estadual;
        $std->CRT = $this->business->crt;
        $std->CNPJ = $this->business->cnpj;
        $this->nfe->tagemit($std);

        $std = new \stdClass();
        $std->xNome = null;
        $std->xLgr = $this->business->logradouro;
        $std->nro = $this->business->numero;
        $std->xBairro = $this->business->bairro;
        $std->cMun = $this->business->ibge;
        $std->xMun = $this->business->cidade;
        $std->UF = $this->business->uf;
        $std->CEP = $this->business->cep;
        $std->cPais = self::COUNTRY_CODE;
        $std->xPais = self::COUNTRY_NAME;
        $this->nfe->tagenderEmit($std);
    }

    private function gen_Destinatario()
    {
        $std = new \stdClass();
        $std->xNome = $this->venda->client->razao;
        $std->indIEDest = self::FREE_ICMS;

        // $std->IE = $this->venda->client->inscricao_estadual;

        if (strlen($this->venda->client->cnpj) == 11) {
            $std->CPF = $this->venda->client->cnpj;
        } else {
            $std->CNPJ = $this->venda->client->cnpj;
        }
        $this->nfe->tagdest($std);

        // $std = new \stdClass();
        // $std->xLgr = $this->venda->cliente->logradouro;
        // $std->nro = $this->venda->cliente->numero;
        // $std->xBairro = $this->venda->cliente->bairro;
        // $std->cMun = $this->venda->cliente->ibge;
        // $std->xMun = $this->venda->cliente->cidade;
        // $std->UF = $this->venda->cliente->uf;
        // $std->CEP = $this->venda->cliente->cep;
        // $std->cPais = self::COUNTRY_CODE;
        // $std->xPais = self::COUNTRY_NAME;
        // $this->nfe->tagenderDest($std);
    }

    private function gen_itens()
    {
        $this->desconto_venda = $this->venda->desconto;
        $this->total_items = 0;
        $this->total_desconto = 0;
        foreach ($this->items as $i => $item) {
            $cfop = "5" . substr($item['produto']['cfop'], 1);

            $std = new \stdClass();
            $std->item = $i = $i + 1;
            $std->cProd = $item['produto']['id'];
            $std->xProd = $item['produto']['descricao'];
            $std->NCM = $item['produto']['ncm'];
            $std->CFOP = $cfop;
            $std->uCom = $item['produto']['medida'];
            $std->uTrib = $std->uCom;
            $std->qTrib = $std->qCom = number_format($item['quantidade'], 2, '.', '');
            $std->vUnTrib = number_format($item['valor_unitario'], 2, '.', '');
            $std->vUnCom = number_format($item['valor_unitario'], 2, '.', '');
            $std->vProd = number_format($std->qCom * $std->vUnCom, 2, '.', '');
            $std->cEAN  = $std->cEANTrib = (!empty($item['produto']['codigo_barras'])) ? $item['produto']['codigo_barras'] : 'SEM GTIN';
            $std->vDesc = ($item['desconto'] > 0) ? $item['desconto'] : null;
            $std->indTot = 1;

            $total_item = $std->vProd - $std->vDesc;

            if ($total_item > $this->desconto_venda && $this->desconto_venda > 0) {
                $std->vDesc += $this->desconto_venda;
                $this->desconto_venda = 0;
            }

            $this->nfe->tagprod($std);



            $this->total_items += $std->vProd;
            $this->total_desconto += $std->vDesc;

            $std = new \stdClass();
            $std->item = $i;
            $this->nfe->tagimposto($std);

            if ($this->business->crt == 1) { //simples nacional

                if ($item['produto']['cst_icms'] == '102') {
                    $std = new \stdClass();
                    $std->item = $i;
                    $std->orig = $item['produto']['origin'];
                    $std->CSOSN = $item['produto']['cst_icms'];
                    $this->nfe->tagICMSSN($std);
                }

                // $std = new \stdClass();
                // $std->item = $i;
                // $std->cEnq = '999';
                // $std->CST = '52';
                // $std->vIPI = 0;
                // $std->vBC = 0;
                // $std->pIPI = 0;
                // $this->nfe->tagIPI($std);

                //PIS - Programa de Integração Social]
                $std = new \stdClass();
                $std->item = $i; //produtos 1
                $std->CST = '07';
                $std->vBC = null;
                $std->pPIS = null;
                $std->vPIS = null;
                $std->qBCProd = null;
                $std->vAliqProd = null;
                $this->nfe->tagPIS($std);

                //COFINS - Contribuição para o Financiamento da Seguridade Social
                $std = new \stdClass();
                $std->item = $i; //produtos 1
                $std->CST = '07';
                $std->vBC = null;
                $std->pCOFINS = null;
                $std->vCOFINS = null;
                $std->qBCProd = null;
                $std->vAliqProd = null;
                $this->nfe->tagCOFINS($std);
            }
        }

        $std = new \stdClass();
        $std->vBC = "0.00";
        $std->vICMS = "0.00";
        $std->vICMSDeson = "0.00";
        $std->vFCP = "0.00"; //incluso no layout 4.00
        $std->vBCST = "0.00";
        $std->vST = "0.00";
        $std->vFCPST = "0.00"; //incluso no layout 4.00
        $std->vFCPSTRet = "0.00"; //incluso no layout 4.00
        $std->vProd = $this->total_items;
        $std->vFrete = "0.00";
        $std->vSeg = "0.00";
        $std->vDesc = $this->total_desconto;
        $std->vII = "0.00";
        $std->vIPI = "0.00";
        $std->vIPIDevol = "0.00"; //incluso no layout 4.00
        $std->vPIS = "0.00";
        $std->vCOFINS = "0.00";
        $std->vOutro = "0.00";
        $std->vNF = $this->total_items - $this->total_desconto;
        $std->vTotTrib = "0.00";

        $this->nfe->tagICMSTot($std);
    }

    private function gen_transporte()
    {
        $std = new \stdClass();
        $std->modFrete = 9;
        $this->nfe->tagtransp($std);

        // if ($std->modFrete != 9) {
        //     $std = new \stdClass();
        //     $std->xNome = $this->nota->transportadora;
        //     $std->IE = $this->nota->transp_inscricao_estadual;
        //     // $std->xEnder = 'Rua Um, sem numero';
        //     // $std->xMun = 'Cotia';
        //     $std->UF = $this->nota->transp_uf;

        //     if (strlen($this->nota->transp_cnpj) > 11) {
        //         $std->CNPJ = $this->nota->transp_cnpj; //só pode haver um ou CNPJ ou CPF, se um deles é especificado o outro deverá ser null
        //     } else {
        //         $std->CPF = $this->nota->transp_cnpj;
        //     }

        //     $this->nfe->tagtransporta($std);

        //     $std = new \stdClass();
        //     $std->placa = $this->nota->transp_placa;
        //     $std->UF = $this->nota->transp_placaUF;
        //     $std->RNTC = null;

        //     $this->nfe->tagveicTransp($std);
        // }
    }

    private function gen_payments()
    {
        $total_pago = 0;
        foreach ($this->payments as $payment) {
            $total_pago += floatval($payment->valor);
        }


        $std = new \stdClass();
        $std->vTroco = $total_pago - ($this->total_items - $this->total_desconto);

        $this->nfe->tagpag($std);

        foreach ($this->payments as $payment) {
            $std = new \stdClass();

            if ($payment->forma == "Dinheiro") {
                $std->indPag = '1';
                $std->tPag = '01';
                $std->vPag = $payment->valor;
            } else {
                $std->indPag = '1';
                $std->tPag = '99';
                $std->vPag = $payment->valor;
            }

            $this->nfe->tagdetPag($std);
        }

        $std = new \stdClass();
        $this->nfe->taginfNFeSupl($std);
    }

    //assinatura
    private function sign_nota()
    {
        try {
            $this->xml = $this->tools->signNFe($this->xml);

            file_put_contents("nfe.xml", $this->xml);

            // $path = "xml/{$this->business->cnpj}/nfce/gerados/{$this->mesPath}/{$this->tpambPath}/{$this->chave}.xml";
            Storage::disk('public')->put("{$this->pathGerados}/{$this->chave}.xml", $this->xml);

            $nota = $this->send_nota();

            return $nota;
        } catch (\Exception $e) {
            return array('erros' => [$e->getMessage()]);
        }
    }

    private function send_nota()
    {
        try {
            $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
            $resp = $this->tools->sefazEnviaLote([$this->xml], $idLote);

            $stdCl    = new Standardize($resp);
            $std      = $stdCl->toStd();

            if ($std->cStat != 103) {
                //erro registrar e voltar
                return ["erros" => array("$std->cStat - $std->xMotivo")];
            }

            $this->recibo = $std->infRec->nRec; // Vamos usar a variável $recibo para consultar o status da nota

            sleep(3);

            $nota = $this->consultarRecibo();

            return $nota;
        } catch (\Exception $e) {
            //aqui você trata possiveis exceptions do envio
            return ["erros" => array($e->getMessage())];
        }
    }

    public function consultarRecibo()
    {
        try {
            $response = $this->tools->sefazConsultaRecibo($this->recibo);

            // TRATA O RETORNO
            $stdCl = new Standardize($response);
            $std   = $stdCl->toStd();

            // SE O LOTE FOI PROCESSADO COM SUCESSO E A NFE FOI AUTORIZADA, INSERI O PROTOCOLO DE AUTORIZAÇÃO NO XML
            if ($std->cStat == '104' && $std->protNFe->infProt->cStat == '100') {
                $xml = Complements::toAuthorize($this->xml, $response);

                $cStatus = $std->protNFe->infProt->cStat;
                $xMotivo = $std->protNFe->infProt->xMotivo;
                $protocolo = $std->protNFe->infProt->nProt;

                $path = "{$this->pathAutorizados}/{$this->chave}.xml";

                Storage::disk('public')->put($path, $xml);

                return array(
                    'venda_id' => $this->venda->id,
                    'emitente_id' => $this->business->id,
                    'sequencia' => $this->numero,
                    'serie' => $this->serie,
                    'tpamb' => $this->tpamb,
                    'cstatus' => $cStatus,
                    'status' => $xMotivo,
                    'recibo' => $this->recibo,
                    'protocolo' => $protocolo,
                    'chave' => $this->chave
                );
            } elseif ($std->cStat == '104' && $std->protNFe->infProt->cStat != '100') {

                return ["erros" => array("{$std->protNFe->infProt->cStat} - {$std->protNFe->infProt->xMotivo}")];
            } else {
                return ["erros" => array("{$std->cStat} - {$std->xMotivo}")];
            }
        } catch (\Exception $e) {
            //aqui você trata possíveis exceptions da consulta
            return ["erros" => array($e->getMessage())];
        }
    }

    public function consultarChave($nota)
    {
        // $mesPath = date('Y-m', strtotime($nota->created_at));
        // $tpambPath = ($nota->tpamb == 1) ? "producao" : "homologacao";

        // $this->config  = [
        //     "atualizacao" => date('Y-m-d h:i:s'),
        //     "tpAmb" => $nota->tpamb,
        //     "razaosocial" => $business->razao,
        //     "cnpj" => $business->cnpj, // PRECISA SER VÁLIDO
        //     "ie" => $business->inscricao_estadual, // PRECISA SER VÁLIDO
        //     "siglaUF" => $business->uf,
        //     "schemes" => "PL_009_V4",
        //     "versao" => self::VERSION,
        //     "tokenIBPT" => "AAAAAAA",
        //     "CSC" => ($nota->tpamb == 1) ? $business->config->csc : $business->config->cscHomolog,
        //     "CSCid" => ($nota->tpamb == 1) ? $business->config->cscid : $business->config->cscidHomolog,
        //     "aProxyConf" => [
        //         "proxyIp" => "",
        //         "proxyPort" => "",
        //         "proxyUser" => "",
        //         "proxyPass" => ""
        //     ]
        // ];

        // $configJson = json_encode($this->config);

        // $certificate = Storage::disk('public')->get("{$business->cnpj}/certificates/{$business->file_pfx}");

        // // try {
        // $tools = new Tools($configJson, Certificate::readPfx($certificate, $business->senha_pfx));
        // $tools->model('55');

        $chave = $nota->chave;
        $response = $this->tools->sefazConsultaChave($chave);

        $stdCl = new Standardize($response);
        $std = $stdCl->toStd();

        //verifique se o evento foi processado
        $cStat = $std->cStat;
        if ($cStat == '100' || $cStat == '101' || $cStat == '135' || $cStat == '155') {

            $nota->cstatus = $std->cStat;
            $nota->status = $std->xMotivo;

            return $nota;
        } else {
            //houve alguma falha no evento
            return ["erros" => ["{$std->cStat} - {$std->xMotivo}"]];
        }

        // return $std;
    }

    public function cancelarNFe($nota)
    {
        // $mesPath = date('Y-m', strtotime($nota->created_at));
        // $tpambPath = ($nota->tpamb == 1) ? "producao" : "homologacao";

        // $this->config  = [
        //     "atualizacao" => date('Y-m-d h:i:s'),
        //     "tpAmb" => $nota->tpamb,
        //     "razaosocial" => $business->razao,
        //     "cnpj" => $business->cnpj, // PRECISA SER VÁLIDO
        //     "ie" => $business->inscricao_estadual, // PRECISA SER VÁLIDO
        //     "siglaUF" => $business->uf,
        //     "schemes" => "PL_009_V4",
        //     "versao" => self::VERSION,
        //     "tokenIBPT" => "AAAAAAA",
        //     "CSC" => ($nota->tpamb == 1) ? $business->config->csc : $business->config->cscHomolog,
        //     "CSCid" => ($nota->tpamb == 1) ? $business->config->cscid : $business->config->cscidHomolog,
        //     "aProxyConf" => [
        //         "proxyIp" => "",
        //         "proxyPort" => "",
        //         "proxyUser" => "",
        //         "proxyPass" => ""
        //     ]
        // ];

        // $configJson = json_encode($this->config);

        // $certificate = Storage::disk('public')->get("{$business->cnpj}/certificates/{$business->file_pfx}");

        // // try {
        // $tools = new Tools($configJson, Certificate::readPfx($certificate, $business->senha_pfx));
        // $tools->model('55');

        $chave = $nota->chave;
        $xJust = $nota->xjust;
        $nProt = $nota->protocolo;
        $response = $this->tools->sefazCancela($chave, $xJust, $nProt);

        $stdCl = new Standardize($response);
        $std = $stdCl->toStd();

        //verifique se o evento foi processado
        if ($std->cStat != 128) {
            //houve alguma falha e o evento não foi processado
            return ["erros" => ["{$std->cStat} - {$std->xMotivo}"]];
        } else {
            $cStat = $std->retEvento->infEvento->cStat;
            if ($cStat == '101' || $cStat == '135' || $cStat == '155') {

                // $pathXML = "{$this->pathRoot}/{$business->cnpj}/xml/nfce/autorizados/{$mesPath}/{$tpambPath}/{$nota->chave}.xml";

                $xmlAutorizado = Storage::disk('public')->get("{$this->pathAutorizados}/{$chave}.xml");

                //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
                $xml = Complements::cancelRegister($xmlAutorizado, $response);

                //grave o XML protocolado
                // $path = "{$business->cnpj}/xml/nfce/cancelados/{$mesPath}/{$tpambPath}/{$nota->chave}.xml";

                Storage::disk('public')->put("{$this->pathCancelados}/{$chave}.xml", $xml);

                $nota->cstatus = $std->retEvento->infEvento->cStat;
                $nota->status = $std->retEvento->infEvento->xMotivo;

                return $nota;
            } elseif ($cStat == '573') {
                $nota->cstatus = "573";
                return $nota;
            } else {
                //houve alguma falha no evento
                return ["erros" => ["{$std->retEvento->infEvento->cStat} - {$std->retEvento->infEvento->xMotivo}"]];
            }
        }
        // } catch (\Exception $e) {
        //    return [$e->getMessage()];
        // }
    }

    public function getPDF(object $dados)
    {

        // if ($dados->cstatus == 100) {
        //     $pathPDF = "pdf/{$business->cnpj}/nfce/autorizados/{$mesPath}/{$tpambPath}/{$dados->chave}.pdf";
        // } else {
        //     $pathPDF = "pdf/{$business->cnpj}/nfce/cancelados/{$mesPath}/{$tpambPath}/{$dados->chave}.pdf";
        // }

        $pathPDF = "{$this->pathPDF}/{$dados->chave}.pdf";

        $existe = Storage::disk('public')->exists($pathPDF);

        if (!$existe) {
            return $this->geraPDF($dados);
        }

        return Storage::url($pathPDF);
    }

    private function geraPDF(object $dados)
    {

        if ($dados->cstatus == 100) {
            $pathXML = "{$this->pathAutorizados}/{$dados->chave}.xml";
        } else {
            $pathXML = "{$this->pathCancelados}/{$dados->chave}.xml";
        }

        $docxml = Storage::disk('public')->get($pathXML);

        $logo = "";

        try {
            $danfe = new Danfce($docxml);
            $danfe->debugMode(false);
            $danfe->creditsIntegratorFooter('TOPP Automação - www.toppautomacao.com.br');

            //Gera o PDF
            $pdf = $danfe->render($logo);

            // if ($dados->cstatus == 100) {
            //     $pathPDF = "pdf/{$business->cnpj}/nfce/autorizados/{$mesPath}/{$tpambPath}/{$dados->chave}.pdf";
            // } else {
            $pathPDF = "{$this->pathPDF}/{$dados->chave}.pdf";
            // }

            Storage::disk('public')->put($pathPDF, $pdf);

            return Storage::url($pathPDF);
        } catch (\InvalidArgumentException $e) {
            echo ["erros" => ["Ocorreu um erro durante o processamento :" . $e->getMessage()]];
        }
    }

    /**
     * Pega o código do estado de acordo com a UF.
     *
     * @return int
     */
    private function _getStateCode($uf)
    {
        return $this->_statesCode[$uf];
    }
}
