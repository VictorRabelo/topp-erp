<?php

namespace App\Repositories;

use App\Mail\NewUserNotification;
use App\Mail\sendXMLs;
use App\Models\NFCe;
use App\Models\NFe;
use Illuminate\Support\Facades\Mail;
use ZipArchive;

class FiscalRepositorie
{

    // function __construct()
    // {
    // }

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
            $notas = NFe::where('emitente_id', $params->emitente_id)->where('created_at', 'like', '%' . $mes . '%');
        } elseif ($params->modelo == "65") {
            $notas = NFCe::where('emitente_id', $params->emitente_id)->where('created_at', 'like', '%' . $mes . '%');
        } else {
        }

        $notas = $notas->orWhere('cstatus', 100)->orWhere('cstatus', 101)->orWhere('cstatus', 135)->orWhere('cstatus', 155)
            ->get();

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
        $path_zip = storage_path("app/public/" . md5($params->empresa_id) . "/zips/{$params->cnpj}");
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
                        $path_locale = storage_path("app/public/{$pathAutorizados}/{$item->chave}.xml");
                    } else {
                        $path_locale = storage_path("app/public/{$pathCancelados}/{$item->chave}.xml");
                    }
                    if (!file_exists($path_locale)) {
                        continue;
                    }
                } elseif ($params->modelo == "65") {
                    $pathAutorizados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfce/{$tpambPath}/{$mesPath}/autorizados";
                    $pathCancelados = md5($params->empresa_id) . "/xml/{$params->cnpj}/nfce/{$tpambPath}/{$mesPath}/cancelados";

                    if ($item->cstatus == 100) {
                        $path_locale = storage_path("app/public/{$pathAutorizados}/{$item->chave}.xml");
                    } else {
                        $path_locale = storage_path("app/public/{$pathCancelados}/{$item->chave}.xml");
                    }
                    if (!file_exists($path_locale)) {
                        continue;
                    }
                } else {
                    // if (!file_exists(public_path("{$params->cnpj}/xml/nfc/{$locationPath}/{$mesPath}/{$tpambPath}/{$item->chave}.xml"))) {
                    //     $path_locale = public_path("{$params->cnpj}/xml/nfe/{$locationPath}/{$mesPath}/{$tpambPath}/{$item->chave}.xml");
                    // } else {
                    //     $path_locale = public_path("{$params->cnpj}/xml/nfc/{$locationPath}/{$mesPath}/{$tpambPath}/{$item->chave}.xml");
                    // }
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
}
