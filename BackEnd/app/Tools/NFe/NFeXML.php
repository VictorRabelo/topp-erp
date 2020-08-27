<?php

namespace App\Tools\NFe;

use Exception;
use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;
use NFePHP\Common\Keys;
use NFePHP\DA\NFe\Danfe;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;

/**
 * Monta o XML de uma NFCe.
 *
 * @author Guilherme Vilela Oliveira <guivo11@gmail.com>
 */
class NFeXML
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
    protected $dados;
    protected $items;
    protected $payments;

    protected $xml;

    private $_statesCode = [
        'RO' => 11, 'AC' => 12, 'AM' => 13, 'RR' => 14, 'PA' => 15, 'AP' => 16,
        'TO' => 17, 'MA' => 21, 'PI' => 22, 'CE' => 23, 'RN' => 24, 'PB' => 25,
        'PE' => 26, 'AL' => 27, 'SE' => 28, 'BA' => 29, 'MG' => 31, 'ES' => 32,
        'RJ' => 33, 'SP' => 35, 'PR' => 41, 'SC' => 42, 'RS' => 43, 'MS' => 50,
        'MT' => 51, 'GO' => 52, 'DF' => 53
    ];

    function __construct(object $business = null, object $dados = null, object $items = null, object $payment = null)
    {
        $this->business = $business;
        $this->dados = $dados;
        $this->items = $items;
        $this->payments = $payment;

        //variaveis global
        if ($business != null) {
            $dataNow = isset($dados->updated_at) ? $dados->updated_at : date('Y-m-d');

            $this->tpamb = $this->business->config->tpamb;
            $this->mesPath = date('Y-m', strtotime($dataNow));
            $this->tpambPath = ($this->tpamb == 1) ? "producao" : "homologacao";
            $this->pathRoot = md5($business->empresa_id);

            if (!Storage::disk('public')->exists("{$this->pathRoot}/fotos/logos/{$this->business->logo}")) {
                $this->logoFile = false;
            } else {
                $this->logoFile = Storage::disk('public')->get("{$this->pathRoot}/fotos/logos/{$this->business->logo}");
            }

            $this->pathGerados = "{$this->pathRoot}/xml/{$this->business->cnpj}/nfe/{$this->tpambPath}/{$this->mesPath}/gerados";
            $this->pathAutorizados = "{$this->pathRoot}/xml/{$this->business->cnpj}/nfe/{$this->tpambPath}/{$this->mesPath}/autorizados";
            $this->pathCancelados = "{$this->pathRoot}/xml/{$this->business->cnpj}/nfe/{$this->tpambPath}/{$this->mesPath}/cancelados";
            $this->pathPDF = "{$this->pathRoot}/pdf/{$this->business->cnpj}/nfe/{$this->tpambPath}/{$this->mesPath}";

            $this->pathMonitor = "{$this->pathRoot}/xml/{$this->business->cnpj}/monitor";

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

            $this->pathCertificate = "{$this->pathRoot}/certificates/{$business->cnpj}/{$business->file_pfx}";

            if (!Storage::disk('public')->exists($this->pathCertificate)) {
                throw new Exception('Parece que o certificado não foi configurado!', '010');
            }

            $this->certificate = Storage::disk('public')->get($this->pathCertificate);

            $contentCertificate = Certificate::readPfx($this->certificate, $business->senha_pfx);

            if ($contentCertificate->isExpired()) {
                throw new Exception('Certificado VENCIDO! Não é possível mais usá-lo!', '010');
            }

            $this->tools = new Tools($this->configJson, $contentCertificate);
            $this->tools->model(55);
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

        //destinatario
        $this->gen_Destinatario();

        //itens da nota
        $this->gen_itens();

        //transportes
        $this->gen_transporte();

        //pagamentos
        $this->gen_payments();

        //informações adicionais
        $std = new \stdClass();
        // $std->infAdFisco = 'informacoes para o fisco';
        $std->infCpl = $this->dados->infor_adicional;
        $this->nfe->taginfAdic($std);

        try {
            $this->xml = $this->nfe->getXml();

            file_put_contents('./nfe.xml', $this->xml);

            Storage::disk('public')->put("{$this->pathGerados}/{$this->chave}.xml", $this->xml);

            return $this->sign_nota();

            return $this->xml;
        } catch (\Exception $e) {
            return ['erros' => $this->nfe->getErrors()];
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
        $std->natOp = $this->dados->natop;
        // $std->indPag = $payment;
        $std->mod = 55;
        $std->serie = $this->serie;
        $std->nNF = $this->numero;
        $std->dhEmi = date('Y-m-d\TH:i:sP', strtotime($this->dados->updated_at));
        $std->tpNF = $this->dados->tipo_nf;
        $std->idDest = ($this->_getStateCode($this->business->uf) != $this->_getStateCode($this->dados->uf)) ? 2 : 1;
        $std->cMunFG = $this->business->ibge;
        $std->tpImp = $this->business->config->tpimp;
        $std->tpEmis = $this->business->config->tpemiss;
        // $std->cDV = 0;
        $std->tpAmb = $this->tpamb;
        $std->finNFe = $this->dados->finalidade_nf;
        $std->indFinal = $this->dados->ind_final;
        $std->indPres = $this->dados->ind_pres;
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

        //verifica se tem referencias na nota
        if (isset($this->dados->references) && $this->dados->finalidade_nf != 1) {

            foreach ($this->dados->references as $reference) {
                $std = new \stdClass();
                $std->refNFe = $reference->chave_nfe;
                $this->nfe->tagrefNFe($std);
            }
        }
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
        $std->xNome = $this->dados->razao;
        $std->indIEDest = $this->dados->inscricao_estadual
            ? self::PAY_ICMS
            : self::FREE_ICMS;
        $this->dados->inscricao_estadual && $std->IE = $this->dados->inscricao_estadual;
        if (strlen($this->dados->cnpj) == 11) {
            $std->CPF = $this->dados->cnpj;
        } else {
            $std->CNPJ = $this->dados->cnpj;
        }
        $this->nfe->tagdest($std);

        $std = new \stdClass();
        $std->xLgr = $this->dados->logradouro;
        $std->nro = $this->dados->numero;
        $std->xBairro = $this->dados->bairro;
        $std->cMun = $this->dados->ibge;
        $std->xMun = $this->dados->cidade;
        $std->UF = $this->dados->uf;
        $std->CEP = $this->dados->cep;
        $std->cPais = self::COUNTRY_CODE;
        $std->xPais = self::COUNTRY_NAME;
        $this->nfe->tagenderDest($std);
    }

    private function gen_itens()
    {
        $this->desconto_venda = $this->dados->desconto;
        $this->total_items = 0;
        $this->total_desconto = 0;

        $this->total_BC = 0;
        $this->total_ICMS = 0;
        $this->total_IPI = 0;
        $this->total_PIS = 0;
        $this->total_COFINS = 0;
        foreach ($this->items as $i => $item) {
            $cfop = ($this->_getStateCode($this->business->uf) != $this->_getStateCode($this->dados->uf))
                ? "6" . substr($item['cfop'], 1)
                : "5" . substr($item['cfop'], 1);

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

                if ($item['cst_icms'] == '102') {
                    $std = new \stdClass();
                    $std->item = $i;
                    $std->orig = $item['produto']['origin'];
                    $std->CSOSN = '102';
                    $this->nfe->tagICMSSN($std);
                } else {
                    $std = new \stdClass();
                    $std->item = $i;
                    $std->orig = $item['produto']['origin'];
                    $std->CSOSN = $item['cst_icms'];
                    $std->vBC = $total_item;
                    $std->pICMS = $item['p_icms'];
                    $std->vICMS = $std->vBC * ($std->pICMS / 100);

                    $this->total_BC += $total_item;
                    $this->total_ICMS += $std->vICMS;

                    $this->nfe->tagICMSSN($std);
                }

                $std = new \stdClass();
                $std->item = $i;
                $std->cEnq = ($item['cst_ipi'] == "52" || $item['cst_ipi'] == "53") ? '301' : '999';
                $std->CST = $item['cst_ipi'];
                $std->vBC = $total_item;
                $std->pIPI = $item['p_ipi'];
                $std->vIPI = $std->vBC * ($std->pIPI / 100);

                $this->total_IPI += $std->vIPI;
                $this->nfe->tagIPI($std);

                //PIS - Programa de Integração Social]
                $std = new \stdClass();
                $std->item = $i; //produtos 1
                $std->CST = $item['cst_pis'];
                $std->vBC = $total_item;
                $std->pPIS = $item['p_pis'];
                $std->vPIS = $std->vBC * ($std->pPIS / 100);
                $std->qBCProd = null;
                $std->vAliqProd = null;

                $this->total_PIS += $std->vPIS;
                $this->nfe->tagPIS($std);

                //COFINS - Contribuição para o Financiamento da Seguridade Social
                $std = new \stdClass();
                $std->item = $i; //produtos 1
                $std->CST = $item['cst_cofins'];
                $std->vBC = $total_item;
                $std->pCOFINS = $item['p_cofins'];
                $std->vCOFINS = $std->vBC * ($std->pCOFINS / 100);
                $std->qBCProd = null;
                $std->vAliqProd = null;

                $this->total_COFINS += $std->vCOFINS;
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
        // $std->vIPI = "0.00";
        $std->vIPIDevol = "0.00"; //incluso no layout 4.00
        // $std->vPIS = "0.00";
        // $std->vCOFINS = "0.00";
        $std->vOutro = "0.00";
        $std->vNF = $this->total_items - $this->total_desconto;
        $std->vTotTrib = "0.00";

        $this->nfe->tagICMSTot($std);
    }

    private function gen_transporte()
    {
        $std = new \stdClass();
        $std->modFrete = $this->dados->mod_frete;
        $this->nfe->tagtransp($std);

        if ($std->modFrete != 9) {
            $std = new \stdClass();
            $std->xNome = $this->dados->transportadora;
            $std->IE = $this->dados->transp_inscricao_estadual;
            // $std->xEnder = 'Rua Um, sem numero';
            // $std->xMun = 'Cotia';
            $std->UF = $this->dados->transp_uf;

            if (strlen($this->dados->transp_cnpj) > 11) {
                $std->CNPJ = $this->dados->transp_cnpj; //só pode haver um ou CNPJ ou CPF, se um deles é especificado o outro deverá ser null
            } else {
                $std->CPF = $this->dados->transp_cnpj;
            }

            $this->nfe->tagtransporta($std);

            $std = new \stdClass();
            $std->placa = $this->dados->transp_placa;
            $std->UF = $this->dados->transp_placaUF;
            $std->RNTC = null;

            $this->nfe->tagveicTransp($std);
        }
    }

    private function gen_payments()
    {

        if ($this->dados->finalidade_nf == 1) {

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
        } else {
            $std = new \stdClass();
            $std->vTroco = 0;
            $this->nfe->tagpag($std);

            $std = new \stdClass();
            $std->tPag = '90';
            $std->vPag = 0;
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

            // $path = "xml/{$this->business->cnpj}/nfe/gerados/{$this->mesPath}/{$this->tpambPath}/{$this->chave}.xml";
            Storage::disk('public')->put("{$this->pathGerados}/{$this->chave}.xml", $this->xml);

            $nota = $this->send_nota();

            return $nota;
        } catch (\Exception $e) {
            return array('erros' => [$e->getMessage()]);
        }
    }

    //envio do xml
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

    //consultar recibo
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
                    'id' => $this->dados->id,
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

    //consultar chave
    public function consultarChave($nota)
    {
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

    public function downloadXML($nota)
    {
        try {
            //este serviço somente opera em ambiente de produção
            $this->tools->setEnvironment(1);

            $chave = $nota->chave;
            $response = $this->tools->sefazDownload($chave);

            // header('Content-type: text/xml; charset=UTF-8');
            // echo $response;

        } catch (\Exception $e) {
            return ['errors' => [str_replace("\n", "<br/>", $e->getMessage())]];
        }
    }

    //cancela a nfe
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

                // $pathXML = "{$this->pathRoot}/{$business->cnpj}/xml/nfe/autorizados/{$mesPath}/{$tpambPath}/{$nota->chave}.xml";

                $xmlAutorizado = Storage::disk('public')->get("{$this->pathAutorizados}/{$chave}.xml");

                //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
                $xml = Complements::cancelRegister($xmlAutorizado, $response);

                //grave o XML protocolado
                // $path = "{$business->cnpj}/xml/nfe/cancelados/{$mesPath}/{$tpambPath}/{$nota->chave}.xml";

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

    //pega o pdf
    public function getPDF(object $dados)
    {

        // if ($dados->cstatus == 100) {
        //     $pathPDF = "pdf/{$business->cnpj}/nfe/autorizados/{$mesPath}/{$tpambPath}/{$dados->chave}.pdf";
        // } else {
        //     $pathPDF = "pdf/{$business->cnpj}/nfe/cancelados/{$mesPath}/{$tpambPath}/{$dados->chave}.pdf";
        // }

        $pathPDF = "{$this->pathPDF}/{$dados->chave}.pdf";

        $existe = Storage::disk('public')->exists($pathPDF);

        if (!$existe) {
            return $this->geraPDF($dados);
        }

        return Storage::url($pathPDF);
    }

    //gera o PDf
    private function geraPDF(object $dados)
    {

        if ($dados->cstatus == 100) {
            $pathXML = "{$this->pathAutorizados}/{$dados->chave}.xml";
        } else {
            $pathXML = "{$this->pathCancelados}/{$dados->chave}.xml";
        }

        $docxml = Storage::disk('public')->get($pathXML);

        $logo = ($this->logoFile != false) ? 'data://text/plain;base64,' . base64_encode($this->logoFile) : "";

        try {
            $danfe = new Danfe($docxml);
            $danfe->debugMode(false);
            $danfe->creditsIntegratorFooter('TOPP Automação - www.toppautomacao.com.br');

            //Gera o PDF
            $pdf = $danfe->render($logo);

            // if ($dados->cstatus == 100) {
            //     $pathPDF = "pdf/{$business->cnpj}/nfe/autorizados/{$mesPath}/{$tpambPath}/{$dados->chave}.pdf";
            // } else {
            $pathPDF = "{$this->pathPDF}/{$dados->chave}.pdf";
            // }

            Storage::disk('public')->put($pathPDF, $pdf);

            return Storage::url($pathPDF);
        } catch (\InvalidArgumentException $e) {
            echo ["erros" => ["Ocorreu um erro durante o processamento :" . $e->getMessage()]];
        }
    }

    //verifica e guarda os XMLS encontrados
    public function monitor_fiscal($ultimoNSU)
    {
        //este serviço somente opera em ambiente de produção
        $this->tools->setEnvironment(1);

        //este numero deverá vir do banco de dados nas proximas buscas para reduzir
        //a quantidade de documentos, e para não baixar várias vezes as mesmas coisas.
        $ultNSU = $ultimoNSU;
        $maxNSU = $ultNSU;
        $loopLimit = 50;
        $iCount = 0;

        $result = array();
        //executa a busca de DFe em loop
        while ($ultNSU <= $maxNSU) {
            $iCount++;
            if ($iCount >= $loopLimit) {
                break;
            }

            try {
                //executa a busca pelos documentos
                $resp = $this->tools->sefazDistDFe($ultNSU);
            } catch (\Exception $e) {
                return array('error' => $e->getMessage());
                //tratar o erro
            }

            //extrair e salvar os retornos
            $dom = new \DOMDocument();
            $dom->loadXML($resp);
            $node = $dom->getElementsByTagName('retDistDFeInt')->item(0);
            $tpAmb = $node->getElementsByTagName('tpAmb')->item(0)->nodeValue;
            $verAplic = $node->getElementsByTagName('verAplic')->item(0)->nodeValue;
            $cStat = $node->getElementsByTagName('cStat')->item(0)->nodeValue;
            $xMotivo = $node->getElementsByTagName('xMotivo')->item(0)->nodeValue;
            $dhResp = $node->getElementsByTagName('dhResp')->item(0)->nodeValue;
            $ultNSU = $node->getElementsByTagName('ultNSU')->item(0)->nodeValue;
            $maxNSU = $node->getElementsByTagName('maxNSU')->item(0)->nodeValue;
            $lote = $node->getElementsByTagName('loteDistDFeInt')->item(0);

            if (empty($lote)) {
                //lote vazio
                continue;
            }

            //essas tags irão conter os documentos zipados
            $docs = $lote->getElementsByTagName('docZip');
            foreach ($docs as $doc) {
                $numnsu = $doc->getAttribute('NSU');
                $schema = $doc->getAttribute('schema');
                //descompacta o documento e recupera o XML original
                $content = gzdecode(base64_decode($doc->nodeValue));
                //identifica o tipo de documento
                $tipo = substr($schema, 0, 6);
                //processar o conteudo do NSU, da forma que melhor lhe interessar
                //esse processamento depende do seu aplicativo

                if ($tipo == "resNFe") {
                    $stdCl = new Standardize($content);
                    $std   = $stdCl->toStd();

                    //variaveis
                    $dados = [
                        'nsu' => $numnsu,
                        'razao' => $std->xNome,
                        'cnpj' => $std->CNPJ,
                        'tpnf' => $std->tpNF,
                        'created_at' => $std->dhEmi,
                        'valor' => $std->vNF,
                        'chave' => $std->chNFe,
                        'nprot' => $std->nProt,
                        'csituacao' => $std->cSitNFe,
                    ];

                    if ($dados['csituacao'] == 1) {
                        $dados['status'] = 'Autorizada';
                    } elseif ($dados['csituacao'] == 2) {
                        $dados['status'] = 'Uso Denegado';
                    } elseif ($dados['csituacao'] == 3) {
                        $dados['status'] = 'Cancelada';
                    }

                    $path = "{$this->pathMonitor}/resumos/{$numnsu}.xml";

                    Storage::disk('public')->put($path, $content);

                    array_push($result, $dados);
                }

                if ($tipo == "procNF") {
                    $stdCl = new Standardize($content);
                    $std   = $stdCl->toStd();

                    $chave = $std->protNFe->infProt->chNFe;

                    //variaveis
                    $dados = [
                        'nsu' => $numnsu,
                        'razao' => $std->NFe->infNFe->emit->xNome,
                        'cnpj' => $std->NFe->infNFe->emit->CNPJ,
                        'tpnf' => $std->NFe->infNFe->ide->tpNF,
                        'create_at' => $std->NFe->infNFe->ide->dhEmi,
                        'numero_nfe' => $std->NFe->infNFe->ide->nNF,
                        'valor' => $std->NFe->infNFe->total->ICMSTot->vNF,
                        'chave' => $std->protNFe->infProt->chNFe,
                        'nprot' => $std->protNFe->infProt->nProt,
                        'status' => $std->protNFe->infProt->xMotivo,
                        'cstatus' => $std->protNFe->infProt->cStat,
                    ];

                    $path = "{$this->pathMonitor}/notas/{$chave}.xml";

                    Storage::disk('public')->put($path, $content);

                    array_push($result, $dados);
                }
            }

            sleep(2);
        }

        return $result;
    }

    public function manifestarNFe($params)
    {
        try {

            $chNFe = $params['chave']; //chave de 44 digitos da nota do fornecedor
            $tpEvento = $params['tpevento']; //ciencia da operação
            $xJust = $params['xjust']; //a ciencia não requer justificativa
            $nSeqEvento = $params['sequencia']; //a ciencia em geral será numero inicial de uma sequencia para essa nota e evento

            $response = $this->tools->sefazManifesta($chNFe, $tpEvento, $xJust, $nSeqEvento);
            //você pode padronizar os dados de retorno atraves da classe abaixo
            //de forma a facilitar a extração dos dados do XML
            //NOTA: mas lembre-se que esse XML muitas vezes será necessário,
            //      quando houver a necessidade de protocolos
            $st = new Standardize($response);
            //nesse caso $std irá conter uma representação em stdClass do XML
            $std = $st->toStd();


            if ($std->cStat != 128) {
                return array('errors' => [$std->cStat . ' - ' . $std->xMotivo]);
            } else {

                $cStatus = $std->retEvento->infEvento->cStat;

                if ($cStatus == 135 || $cStatus == 573) {
                    $params['status'] = $std->retEvento->infEvento->xMotivo;

                    $params['cstatus'] = $std->retEvento->infEvento->cStat;

                    $params['evento'] = $std->retEvento->infEvento->xEvento;

                    // $params['nprot'] = $std->retEvento->infEvento->nProt;

                    $path = "{$this->pathMonitor}/eventos/{$chNFe}/{$nSeqEvento}-{$tpEvento}.xml";

                    Storage::disk('public')->put($path, $response);

                    return $params;
                } else {
                    return array('errors' => [$std->retEvento->infEvento->cStat . ' - ' . $std->retEvento->infEvento->xMotivo]);
                }
            }
        } catch (\Exception $e) {
            return array('errors' => [$e->getMessage()]);
        }
    }

    //associa a uf ao codigo da uf
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
