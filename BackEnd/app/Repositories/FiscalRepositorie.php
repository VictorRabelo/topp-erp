<?php

namespace App\Repositories;

use App\Mail\NewUserNotification;
use App\Mail\sendXMLs;
use App\Models\Emitente;
use App\Models\EmitenteConfig;
use App\Models\MonitorFiscal;
use App\Models\MonitorFiscalEventos;
use App\Models\NFCe;
use App\Models\NFe;
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
            // if (count($eventos) > 0) {
            //     $item->eventos = $eventos;
            // }
            // if ($exist) {
            //     $item->pathXML = Storage::url("{$pathRoot}/xml/{$emitente->cnpj}/monitor/notas/{$item->chave}.xml");
            // }
        }

        return $monitor;
    }

    public function getMonitorSefaz($params)
    {
        $emitente_id = $params['emitente_id'];
        $emitente = Emitente::find($emitente_id);
        $emitente->config = EmitenteConfig::where('emitente_id', $emitente_id)->where('modelo', 55)->first();

        $utimoNSU = MonitorFiscal::where('emitente_id', $emitente_id)->orderBy('nsu', 'desc')->first();
        $nsu = ($utimoNSU->nsu > 0) ? $utimoNSU->nsu : 0;

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
                MonitorFiscal::insert($dados);
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
}
