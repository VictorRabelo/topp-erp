<?php

namespace App\Tools\NFCe;

use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;
use NFePHP\Common\Keys;
use NFePHP\DA\NFe\Danfce;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;

/**
 * Monta o XML de uma NFCe.
 *
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
   public const FREE_ICMS = 2;

   private $_vBC = 0;

   private $_statesCode = [
      'RO' => 11, 'AC' => 12, 'AM' => 13, 'RR' => 14, 'PA' => 15, 'AP' => 16,
      'TO' => 17, 'MA' => 21, 'PI' => 22, 'CE' => 23, 'RN' => 24, 'PB' => 25,
      'PE' => 26, 'AL' => 27, 'SE' => 28, 'BA' => 29, 'MG' => 31, 'ES' => 32,
      'RJ' => 33, 'SP' => 35, 'PR' => 41, 'SC' => 42, 'RS' => 43, 'MS' => 50,
      'MT' => 51, 'GO' => 52, 'DF' => 53
   ];

   /**
    * Constrói o XML.
    *
    * @return mixed
    */
   public function make(object $business, object $venda, object $client, object $items, object $payments)
   {

      $this->nfe = $nfe = new Make();
      $this->tpamb = $business->config->tpamb;

      $this->empresa = $business;
      $this->venda = $venda;
      $this->numero = ($this->tpamb == 1) ? $business->config->seq : $business->config->seqHomolog;

      $this->cnpj = $business->cnpj;
      $this->mesPath = date('Y-m');
      $this->tpambPath = ($this->tpamb == 1) ? "producao" : "homologacao";


      $std = new \stdClass();
      $std->versao = self::VERSION;
      $nfe->taginfNFe($std);

      $std = new \stdClass();
      $std->cUF = $this->_getStateCode($business->uf);
      $std->cNF = mt_rand(10000000, 99999999);
      $std->natOp = self::NATURE_OPERATION;
      // $std->indPag = $payment;
      $std->mod = 65;
      $std->serie = ($this->tpamb == 1) ? $business->config->serie : $business->config->serieHomolog;
      $std->nNF = ($this->tpamb == 1) ? $business->config->seq : $business->config->seqHomolog;
      $std->dhEmi = date('Y-m-d\TH:i:sP');
      $std->tpNF = $business->config->tpnf;
      $std->idDest = self::INTERN_OPERATION;
      $std->cMunFG = $business->ibge;
      $std->tpImp = $business->config->tpimp;
      $std->tpEmis = $business->config->tpemiss;
      $std->tpAmb = $this->tpamb;
      $std->finNFe = 1;
      $std->indFinal = 1;
      $std->indPres = 1;
      $std->procEmi = 0;
      $std->verProc = '1.0.0';

      // Monta Chave da NF-e
      $this->chave = Keys::build(
         $std->cUF,
         date('y', strtotime($std->dhEmi)),
         date('m', strtotime($std->dhEmi)),
         $business->cnpj,
         $std->mod,
         $std->serie,
         $std->nNF,
         $std->tpEmis,
         $std->cNF
      );

      $std->cDV = substr($this->chave, -1);

      $nfe->tagide($std);

      $std = new \stdClass();
      $std->xNome = $business->razao;
      $std->IE = $business->inscricao_estadual;
      $std->CRT = $business->crt;
      $std->CNPJ = $business->cnpj;
      $nfe->tagemit($std);

      $std = new \stdClass();
      $std->xLgr = $business->logradouro;
      $std->nro = $business->numero;
      $std->xBairro = $business->bairro;
      $std->cMun = $business->ibge;
      $std->xMun = $business->cidade;
      $std->UF = $business->uf;
      $std->CEP = $business->cep;
      $std->cPais = self::COUNTRY_CODE;
      $std->xPais = self::COUNTRY_NAME;
      $nfe->tagenderEmit($std);


      if (isset($client->id)) {

         $std = new \stdClass();
         $std->xNome = $client->razao;
         $std->indIEDest = $client->inscricao_estadual
            ? self::PAY_ICMS
            : self::FREE_ICMS;
         $client->inscricao_estadual && $std->IE = $client->inscricao_estadual;
         if (strlen($client->cnpj) == 11) {
            $std->CPF = $client->cnpj;
         } else {
            $std->CNPJ = $client->cnpj;
         }
         $nfe->tagdest($std);

         // $std = new \stdClass();
         // $std->xLgr = $client->logradouro;
         // $std->nro = $client->numero;
         // $std->xBairro = $client->bairro;
         // $std->cMun = $client->ibge;
         // $std->xMun = $client->cidade;
         // $std->UF = $client->uf;
         // $std->CEP = $client->cep;
         // $std->cPais = self::COUNTRY_CODE;
         // $std->xPais = self::COUNTRY_NAME;
         // $nfe->tagenderDest($std);

      } elseif (isset($client->cpf)) {
         $std->xNome = $client->cliente;
         $std->indIEDest = 2;
         if (strlen($client->cpf) == 11) {
            $std->CPF = $client->cpf;
         } else {
            $std->CNPJ = $client->cpf;
         }
      }

      $desconto_venda = $venda->desconto;
      $total_items = 0;
      $total_desconto = 0;
      foreach ($items as $i => $item) {
         $std = new \stdClass();
         $std->item = $i = $i + 1;
         $std->cProd = $item['produto']['id'];
         $std->xProd = $item['produto']['descricao'];
         $std->NCM = $item['produto']['ncm'];
         $std->CFOP = $item['produto']['cfop'];
         $std->uCom = $item['produto']['medida'];
         $std->uTrib = $item['produto']['medida'];
         $std->qTrib = $std->qCom = number_format($item['quantidade'], 2, '.', '');
         $std->vProd = number_format($item['quantidade'] * $item['valor_unitario'], 2, '.', '');
         $std->vUnTrib = $std->vUnCom = number_format($item['valor_unitario'], 2, '.', '');
         $std->cEAN     = $std->cEANTrib = (!empty($item['produto']['codigo_barras'])) ? $item['produto']['codigo_barras'] : 'SEM GTIN';
         $std->vDesc = ($item['desconto'] > 0) ? $item['desconto'] : null;
         $std->indTot = 1;

         $total_item = $std->vProd - $std->vDesc;

         if ($total_item > $desconto_venda && $desconto_venda > 0) {
            $std->vDesc += $desconto_venda;
            $desconto_venda = 0;
         }

         $nfe->tagprod($std);

         $total_items += $std->vProd;
         $total_desconto += $std->vDesc;


         $std = new \stdClass();
         $std->item = $i;
         $nfe->tagimposto($std);

         if ($business->crt == 1) { //simples nacional

            if ($item['produto']['cst'] == '102') {
               $std = new \stdClass();
               $std->item = $i;
               $std->orig = $item['produto']['origin'];
               $std->CSOSN = $item['produto']['cst'];
               $nfe->tagICMSSN($std);
            }

            //PIS - Programa de Integração Social]
            $std = new \stdClass();
            $std->item = $i; //produtos 1
            $std->CST = '07';
            $std->vBC = null;
            $std->pPIS = null;
            $std->vPIS = null;
            $std->qBCProd = null;
            $std->vAliqProd = null;
            $nfe->tagPIS($std);

            //COFINS - Contribuição para o Financiamento da Seguridade Social
            $std = new \stdClass();
            $std->item = $i; //produtos 1
            $std->CST = '07';
            $std->vBC = null;
            $std->pCOFINS = null;
            $std->vCOFINS = null;
            $std->qBCProd = null;
            $std->vAliqProd = null;
            $nfe->tagCOFINS($std);
         }
      }

      $std = new \stdClass();
      $std->vProd     = number_format($total_items, 2, '.', '');
      $std->vDesc     = $total_desconto;
      $nfe->tagICMSTot($std);

      $std = new \stdClass();
      $std->modFrete = 9;
      $nfe->tagtransp($std);

      $total_pago = 0;
      foreach ($payments as $payment) {
         $total_pago += floatval($payment->valor);
      }


      $std = new \stdClass();
      $std->vTroco = $total_pago - ($total_items - $total_desconto);

      $nfe->tagpag($std);

      foreach ($payments as $payment) {
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

         $nfe->tagdetPag($std);
      }

      $std = new \stdClass();
      $nfe->taginfNFeSupl($std);

      try {
         $xml = $nfe->getXml();

         $path = "{$this->cnpj}/xml/nfc/gerados/{$this->mesPath}/{$this->tpambPath}/{$this->chave}.xml";
         Storage::disk('public')->put($path, $xml);

         file_put_contents("nfc.xml", $xml);

         $xml = $this->assinar($business, $xml);

         return $xml;
      } catch (\Exception $e) {
         return $nfe->getErrors();
      }
   }


   function assinar($business, $xml)
   {
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

      $configJson = json_encode($this->config);

      $certificate = Storage::disk('public')->get("{$business->cnpj}/certificates/{$business->file_pfx}");

      $tools = new Tools($configJson, Certificate::readPfx($certificate, $business->senha_pfx));
      $tools->model(65);

      try {
         $xml_sign = $tools->signNFe($xml);

         file_put_contents("nfc.xml", $xml_sign);

         $path = "{$this->cnpj}/xml/nfc/gerados/{$this->mesPath}/{$this->tpambPath}/{$this->chave}.xml";
         Storage::disk('public')->put($path, $xml);

         $xml_sign = $this->enviar($xml_sign, $tools);

         return $xml_sign;
      } catch (\Exception $e) {
         return array($e->getMessage());
      }
   }

   function enviar($xml, $tools)
   {
      try {
         $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
         $resp = $tools->sefazEnviaLote([$xml], $idLote);

         $stdCl    = new Standardize($resp);
         $std      = $stdCl->toStd();

         if ($std->cStat != 103) {
            //erro registrar e voltar
            return array("$std->cStat - $std->xMotivo");
         }

         $recibo = $std->infRec->nRec; // Vamos usar a variável $recibo para consultar o status da nota

         sleep(3);

         $nota = $this->consultarRecibo($xml, $recibo, $tools);

         return $nota;

         // return array(
         //    'empresa_id' => $this->empresa->id,
         //    'venda_id' => $this->venda->id,
         //    'numero' => $this->numero,
         //    'tpamb' => $this->tpamb,
         //    'cstatus' => $std->cStat,
         //    'status' => $std->xMotivo,
         //    'recibo' => $recibo,
         //    'chave' => $this->chave
         // );

      } catch (\Exception $e) {
         //aqui você trata possiveis exceptions do envio
         return array($e->getMessage());
      }
   }

   public function consultarRecibo($xml, $recibo, $tools)
   {
      try {
         $response = $tools->sefazConsultaRecibo($recibo);

         // TRATA O RETORNO
         $stdCl = new Standardize($response);
         $std   = $stdCl->toStd();

         // SE O LOTE FOI PROCESSADO COM SUCESSO E A NFE FOI AUTORIZADA, INSERI O PROTOCOLO DE AUTORIZAÇÃO NO XML
         if ($std->cStat == '104' && $std->protNFe->infProt->cStat == '100') {
            $xml = Complements::toAuthorize($xml, $response);

            $cStatus = $std->protNFe->infProt->cStat;
            $xMotivo = $std->protNFe->infProt->xMotivo;
            $protocolo = $std->protNFe->infProt->nProt;

            $path = "{$this->cnpj}/xml/nfc/autorizados/{$this->mesPath}/{$this->tpambPath}/{$this->chave}.xml";

            Storage::disk('public')->put($path, $xml);

            return array(
               'empresa_id' => $this->empresa->id,
               'venda_id' => $this->venda->id,
               'numero' => $this->numero,
               'tpamb' => $this->tpamb,
               'cstatus' => $cStatus,
               'status' => $xMotivo,
               'recibo' => $recibo,
               'protocolo' => $protocolo,
               'chave' => $this->chave
            );
         } elseif ($std->cStat == '104' && $std->protNFe->infProt->cStat != '100') {

            return array("{$std->protNFe->infProt->cStat} - {$std->protNFe->infProt->xMotivo}");
         } else {
            return array("{$std->cStat} - {$std->xMotivo}");
         }
      } catch (\Exception $e) {
         //aqui você trata possíveis exceptions da consulta
         return array($e->getMessage());
      }
   }


   public function geraPDF($nota)
   {
      // try {

         $mesPath = date('Y-m', strtotime($nota->created_at));
         $tpambPath = ($nota->tpamb == 1) ? "producao" : "homologacao";

         $pathXML = "{$nota->cnpj}/xml/nfc/autorizados/{$mesPath}/{$tpambPath}/{$nota->chave}.xml";

         $docxml = Storage::disk('public')->get($pathXML);

         $logo = "";

         $danfce = new Danfce($docxml);
         $danfce->debugMode(false);
         $danfce->creditsIntegratorFooter('TOPP Automação - www.toppautomacao.com.br');
         $pdf = $danfce->render($logo);

         $pathPDF = "{$nota->cnpj}/pdf/nfc/{$mesPath}/{$tpambPath}/{$nota->chave}.pdf";
         Storage::disk('public')->put($pathPDF, $pdf);

         return Storage::url($pathPDF);
   }

   public function getPDF($nota)
   {
      $mesPath = date('Y-m', strtotime($nota->created_at));
      $tpambPath = ($nota->tpamb == 1) ? "producao" : "homologacao";

      $pathPDF = "{$nota->cnpj}/pdf/nfc/{$mesPath}/{$tpambPath}/{$nota->chave}.pdf";

      $existe = Storage::disk('public')->exists($pathPDF);

      if (!$existe) {
         return $existe;
      }

      return $pdf = Storage::url($pathPDF);
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
