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
      $query = $this->model->where('empresa_id', $this->user->empresa_id)->get();

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
      $verificaNota = $this->model->where('empresa_id', $emitente->id)
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
         $dados = $this->model->where('empresa_id', $response['empresa_id'])
            ->where('tpamb', $response['tpamb'])
            ->where('numero', $response['numero'])
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

      $nfc = $this->model->create($data);

      return $nfc;
   }

   public function editar($post, int $id)
   {
      $nfc = $this->model->find($id);
      $nfc->fill($post);
      $nfc->save();

      return $nfc;
   }

   public function delete(int $id)
   {
      $dados = $this->model->find($id);
      return $dados->delete();
   }

   public function printNota(object $nota)
   {
      $emitente = Emitente::find($nota->empresa_id);
      $nota->cnpj = $emitente->cnpj;
      
      $pdf_url = $this->nfce->getPDF($nota);

      return array('pdf_url' => $pdf_url);
   }
}
