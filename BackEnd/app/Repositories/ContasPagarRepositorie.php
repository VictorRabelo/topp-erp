<?php

namespace App\Repositories;

use App\Models\Caixa;
use App\Models\ContasPagar;
use Illuminate\Support\Facades\Auth;

class ContasPagarRepositorie
{
    function __construct()
    {
        $this->model = new ContasPagar();
        $this->user = Auth::guard('api')->user();
    }

    public function getList($params)
    {
        $query = $this->model->where('empresa_id', $this->user->empresa_id)->limit(20);

        if (!empty($params['status'])) {
            $query = $query->where('situacao', $params['status']);
        }

        $query = $query->get();

        return $query;
    }

    public function create($params)
    {

        if (isset($params['parcelas']) && $params['parcelas'] > 1) {
            $currentData = $params['vencimento'];

            $dia = date('d', strtotime($currentData));
            $mes = date('m', strtotime($currentData));
            $ano = date('Y', strtotime($currentData));

            for ($i = 0; $i < $params['parcelas']; $i++) {

                $params['empresa_id'] = $this->user->empresa_id;
                $conta = $this->model->create($params);
                $conta->documento = str_pad($conta->id, 8, "0", STR_PAD_LEFT);
                $conta->save();

                $mes = $mes + 1;
                if ($mes > 12) {
                    $mes = 1;
                    // ++$ano;
                    $ano = $ano + 1;
                }

                $diap = $dia;

                if ($mes == 2 && $dia > 28) {
                    $diap = 28;
                }
                if ($mes == 4 && $dia > 30 || $mes == 6 && $dia > 30 || $mes == 9 && $dia > 30 || $mes == 11 && $dia > 30) {
                    $diap = 30;
                }


                $newDate = date("Y-m-d", mktime(0, 0, 0, $mes, $diap, $ano));

                // $conta->save();

                $params['vencimento'] = $newDate;
            }

            return true;
        } else {
            $params['empresa_id'] = $this->user->empresa_id;
            $conta = $this->model->create($params);
            $conta->documento = str_pad($conta->id, 8, "0", STR_PAD_LEFT);
            $conta->save();

            return $conta;
        }
    }

    public function update($params, $id)
    {
        $conta = $this->model->find($id);
        $conta->fill($params);
        $conta->save();

        return $conta;
    }

    public function paymentConta($params)
    {
        $payments = $params['payments'];
        $_dados = $params['dados'];
        $contas = $params['contas'];

        foreach ($contas as $conta) {
            // print_r($conta);
            $dados = $this->model->find($conta['id']);
            $dados->fill($conta);

            $dados->valor_pago = $conta['valor_pago'];
            $dados->situacao = 10;
            $dados->data_pago = $_dados['data_pago'];

            $dados->save();
        }

        $this->geraCaixa($payments, $_dados);

        return true;
    }

    private function geraCaixa($payments, $dados)
    {
        foreach ($payments as $payment) {
            // print_r($payment);
            $caixa = new Caixa();
            $caixa->empresa_id = $this->user->empresa_id;
            // $caixa->cliente_id = $conta->cliente_id;
            $caixa->forma_id = $payment['id'];
            $caixa->forma = $payment['forma'];
            $caixa->tipo = 2;
            $caixa->descricao = "Ref. " . $dados['descricao'] . " - " . $payment['forma'];
            $caixa->valor = ($payment['resto'] > $payment['valor']) ? $payment['valor'] : $payment['resto'];
            $caixa->save();
        }
    }
}
