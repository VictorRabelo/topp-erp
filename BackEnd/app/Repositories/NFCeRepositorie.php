<?php

namespace App\Repositories;

use App\models\NFCe;
use App\models\Emitente;
use App\models\EmitenteConfig;
use App\models\Client;
use App\models\Venda;
use App\models\VendaItens;
use App\Tools\NFCe\NFCeXML;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NFCeRepositorie
{
   function __construct()
   {
      $this->model = new NFCe();
      $this->user = Auth::guard('api')->user();
      $this->nfce = new NFCeXML();
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
         $cliente = Client::find($venda->cliente_id);
      } elseif (!empty($venda->cpf)) {
         $cliente = (object) ['cliente' => $venda->cliente, 'cpf' => $venda->cpf];
      } else {
         $cliente = (object) [];
      }

      //dados dos itens
      $itens = VendaItens::with('produto')->where('venda_id', $venda_id)->get();
      //dados dos pagamentos
      $payments = DB::table('vendas_payments')->where('venda_id', $venda_id)->get();


      //verifica se ha nota emitida com esses dados
      $verificaNota = $this->model->where('emitente_id', $emitente->id)
         ->where('tpamb', $emitente->config->tpamb)
         ->where('venda_id', $venda_id)
         ->orderBy('id', 'desc')
         ->first();

      if (!empty($verificaNota)) {
         $verificaNota->cnpj = $emitente->cnpj;
         $urlPDF = $this->nfce->getPDF($verificaNota);
         if ($urlPDF == false) {
            return $this->nfce->geraPDF($verificaNota);
         }
         return $urlPDF;
      }


      //monta xml, assina e envia para sefaz
      $response = $this->nfce->make($emitente, $venda, $cliente, $itens, $payments);

      //verifica se tem recibo
      if (isset($response['protocolo'])) {

         //verifica se ja existe registro parecido
         $dados = $this->model->where('emitente_id', $response['emitente_id'])
            ->where('tpamb', $response['tpamb'])
            ->where('numero', $response['numero'])
            ->orderBy('id', 'desc')
            ->first();

         if (empty($dados)) {
            $nota = $this->create($response);
         } else {
            $nota = $this->editar($response, $dados->id);
         }

         $nota->cnpj = $emitente->cnpj;
         $pdf_url = $this->nfce->geraPDF($nota);

         if ($nota->id) {

            if ($nota->tpamb == 1) {
               $emitente->config->seq += 1;
            } else {
               $emitente->config->seqHomolog += 1;
            }

            $emitente->config->save();
         }

         //retorna os dados
         return $pdf_url;
      }

      //retorna caso não tenha recibo
      return $response;
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
      $nota = $this->model->find($id);
      $nota->xjust = $data['xjust'];

      $emitente = Emitente::find($nota->emitente_id);
      $emitente->config = EmitenteConfig::where('emitente_id', $nota->emitente_id)->where('modelo', 65)->first();

      $nota->cnpj = $emitente->cnpj;

      if ($nota->cstatus == 101 || $nota->cstatus == 135 || $nota->cstatus == 155) {
         return $this->consultaNota($nota, $emitente);
      }

      $response = $this->nfce->cancelarNFCe($nota, $emitente);
      // print_r($response);

      //verifica se tem recibo
      if (isset($response['protocolo'])) {

         //verifica se ja existe registro parecido
         // $nota = $this->editar($response, $nota->id);

         // $urlPDF = $this->nfce->getPDF($nota);
         // if ($urlPDF == false) {
         //    $urlPDF = $this->nfce->geraPDF($nota);
         // }

         $this->consultaNota($nota, $emitente);

         unset($nota->cnpj);
         $nota = $response->save();

         return $nota;
      }

      return $response;
   }
   function consultaNota($nota, $emitente)
   {
      $consulta = $this->nfce->consultarChave($nota, $emitente);
      if (isset($consulta->cstatus)) {
         unset($nota->cnpj);
         return $nota =  $consulta->save();
      }

      return $consulta;
   }

   public function printNota(object $nota)
   {
      $emitente = Emitente::find($nota->emitente_id);
      $nota->cnpj = $emitente->cnpj;

      $urlPDF = $this->nfce->getPDF($nota);
      if ($urlPDF == false) {
         return array('pdf_url' => $this->nfce->geraPDF($nota));
      }

      return array('pdf_url' => $urlPDF);
   }
}
