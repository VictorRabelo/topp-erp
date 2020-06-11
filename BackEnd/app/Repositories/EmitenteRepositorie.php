<?php

namespace App\Repositories;

use App\models\Emitente;
use App\models\EmitenteConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmitenteRepositorie
{
   function __construct()
   {
      $this->model = new Emitente();
      $this->user = Auth::guard('api')->user();
   }

   public function list($params)
   {
      $query = $this->model->where('empresa_id', $this->user->empresa_id)->get();
      return $query;
   }

   public function create($data)
   {
      $data['empresa_id'] = $this->user->empresa_id;
      $create = $this->model->create($data);

      if ($create->id) {
         $configNFe = EmitenteConfig::create(['emitente_id' => $create->id, 'modelo' => 55]);
         $configNFCe = EmitenteConfig::create(['emitente_id' => $create->id, 'modelo' => 65]);
         if (isset($data['file']) && $data['file'] != "") {
            $folder = $data['cnpj'] . "/fotos";

            $create->logo = $this->parse_file($data, $folder);
            $create->save();
         }
      }

      return $create;
   }

   public function getById(int $id)
   {
      $dados = $this->model->find($id);

      $configNFe = EmitenteConfig::where('emitente_id', $dados['id'])->where('modelo', 55)->first();
      $configNFCe = EmitenteConfig::where('emitente_id', $dados['id'])->where('modelo', 65)->first();

      if (!empty($dados->file_pfx)) {
         $dados['certificate_url'] = $this->set_file($dados->file_pfx, "{$dados->cnpj}/certificates");
      }
      if (!empty($dados->logo)) {
         $dados['logo_url'] = $this->set_file($dados->logo, "{$dados->cnpj}/fotos");
      }

      $return = array('dados' => $dados, 'nfe' => $configNFe, 'nfce' => $configNFCe);

      return $return;
   }

   public function update(array $data, $id)
   {
      $dados = $this->model->find($id);
      $dados->fill($data);

      if (isset($data['file']) && $data['file'] != "") {
         $folder = $data['cnpj'] . "/fotos";

         $dados->logo = $this->parse_file($data, $folder);
      }
      return $dados->save();
   }

   public function delete(int $id)
   {
      $resp = $this->model->find($id);
      return $resp->delete();
   }

   //configurações
   public function getByIdConfig(int $id)
   {
      $dados = EmitenteConfig::find($id);
      return $dados;
   }
   public function updateConfig($data, $id)
   {
      $dados = EmitenteConfig::find($id);
      $dados->fill($data);
      return $dados->save();
   }

   //certificados
   public function updateCertificate($data, int $id)
   {
      $dados = $this->model->find($id);

      if (isset($data['file']) && $data['file'] != "") {
         $folder = $data['cnpj'] . "/certificates";

         $dados->file_pfx = $this->parse_file($data, $folder);
      }

      $dados->senha_pfx = $data['senha_pfx'];

      return $dados->save();
   }

   // utilidades
   private function parse_file($data, $folder)
   {
      if (isset($data['file']) && $data['file'] != '') {

         $this->_deleteFileIfExists($data, $folder);

         $content = base64_decode($data['file'][0]);
         $file = fopen('php://temp', 'r+');
         fwrite($file, $content);
         $file_name = md5(
            uniqid(
               microtime(),
               true
            )
         ) . '.' . pathinfo($data['file_name'], PATHINFO_EXTENSION);

         Storage::disk('public')
            ->put("{$folder}/" . $file_name, $file);
         return $file_name;
      }
   }
   private function _deleteFileIfExists(array $data, $folder): void
   {
      if (array_key_exists('file_pfx', $data) && $data['file_pfx'] != null) {
         Storage::disk('public')
            ->delete("{$folder}/" . $data['file_pfx']);
      }
   }
   private function set_file($file, $folder)
   {
      $exists = Storage::disk('public')->exists("{$folder}/" . $file);
      if (!$exists) {
         return null;
      }
      return Storage::url("{$folder}/" . $file);
   }
}
