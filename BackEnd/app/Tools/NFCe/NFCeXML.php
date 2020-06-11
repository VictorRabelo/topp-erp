<?php

namespace App\Tools;

use NFePHP\NFe\Make;

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
   public function make(object $payments, object $client, object $business, object $items)
   {

      $this->nfe = $nfe = new Make();
      $this->tpamb = $business->config->tpamb;

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
      $std->nNF = ($this->tpamb == 1) ? $business->config->serie : $business->config->serieHomolog;
      $std->dhEmi = date('Y-m-d\TH:i:sP');
      $std->tpNF = $business->config->tpnf;
      $std->idDest = self::INTERN_OPERATION;
      $std->cMunFG = $business->ibge;
      $std->tpImp = $business->config->tpimp;
      $std->tpEmis = $business->config->tpemi;
      $std->cDV = 0;
      $std->tpAmb = $this->tpamb;
      $std->finNFe = 1;
      $std->indFinal = 1;
      $std->indPres = 1;
      $std->procEmi = 0;
      $std->verProc = 0;
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
         if (strlen($client->cpf) == 11) {
            $std->CPF = $client->cpf;
         } else {
            $std->CNPJ = $client->cpf;
         }
      }

      $total_items = 0;
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
         $std->vProd = number_format($item['total'], 2, '.', '');
         $total_items += $std->vUnTrib = $std->vUnCom = number_format($item['valor_unitario'], 2, '.', '');
         $std->cEANTrib = 'SEM GTIN';
         $std->cEAN     = 'SEM GTIN';
         $std->indTot = 1;
         $nfe->tagprod($std);

         $std = new \stdClass();
         $std->item = $i;
         $nfe->tagimposto($std);

         $std = new \stdClass();
         $std->item = $i;
         $std->orig = 0;
         $std->pCredSN = 0;
         $std->vCredICMSSN = 0;
         $std->modBC = 0;
         $std->vBC = number_format($item['total'], 2, '.', '');
         $this->_vBC += $item['total'];
         $std->vICMS = $item['valor_unitario'];
         $std->CSOSN = $item['produto']['cst'];
         $nfe->tagICMSSN($std);
      }
      $std = new \stdClass();
      $nfe->tagICMSTot($std);

      $std = new \stdClass();
      $std->modFrete = 9;
      $nfe->tagtransp($std);

      $total_pago = 0;
      foreach ($payments as $payment) {
         $total_pago += floatval($payment->valor);
      }


      $std = new \stdClass();
      $std->vTroco = $total_pago - floatval($this->_vBC);

      $nfe->tagpag($std);

      foreach ($payments as $payment) {
         $std = new \stdClass();

         if ($payment->forma == "Dinheiro") {
            $std->indPag = '1';
            $std->tPag = '1';
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
         file_put_contents("nfc.xml", $xml);

         return $xml;
      } catch (\Exception $e) {
         return $nfe->getErrors();
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
