<?php

namespace App\Repositories;

use App\Models\Caixa;
use App\Models\Payment;
use App\Models\Venda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceiroRepositorie
{
    function __construct()
    {
        // $this->model = new Product();
        $this->user = Auth::guard('api')->user();
        $this->dateNow = date('Y-m-d');
    }

    public function resumoCaixa($params)
    {
        // $this->dateNow = "2020-07-28";
        if ($params['tipo'] == 1) {
            if (isset($params['data_ini']) && !empty($params['data_ini'])) {
                $data = $params['data_ini'];

                $movs = Caixa::where('data', 'like', '%' . $data . '%');
            } else {
                $data = $this->dateNow;

                $movs = Caixa::where('data', 'like', '%' . $data . '%');
            }
        } else {
            if (
                (isset($params['data_ini']) && !empty($params['data_ini']))
                &&
                (isset($params['data_fim']) && !empty($params['data_fim']))
            ) {
                $data_ini = $params['data_ini'];
                $data_fim = $params['data_fim'];

                $movs = Caixa::whereBetween('data', [$data_ini, $data_fim]);
            } elseif (isset($params['data_ini']) && !empty($params['data_ini'])) {
                $data = $params['data_ini'];

                $movs = Caixa::where('data', 'like', '%' . $data . '%');
            } else {
                $data = $this->dateNow;

                $movs = Caixa::where('data', 'like', '%' . $data . '%');
            }
        }

        $movs = $movs->where('empresa_id', $this->user->empresa_id)->get();

        $total_entradas = 0;
        $total_saidas = 0;
        foreach ($movs as $mov) {
            if ($mov->tipo == 1) { //entrada
                $total_entradas += $mov->valor;
            } else { //saida
                $total_saidas += $mov->valor;
            }
        }

        $data = $this->dateNow;
        $payments = DB::table('caixa')
            ->select(
                DB::raw('SUM(caixa.valor) as total'),
                'caixa.*'
            )
            ->where('caixa.data', 'like', '%' . $data . '%')
            ->orderBy('caixa.forma_id', 'asc')
            ->groupBy('caixa.forma_id')
            ->get();

        // $total_pago = 0;
        foreach ($payments as $payment) {
            // $total_pago += $payment->valor;
            if ($payment->tipo == 2) { //subtrai o valor de saída
                $payment->total -= $payment->valor;
            }
        }

        $dados = [
            'total_entradas' => $total_entradas,
            'total_saidas' => $total_saidas,
            'total' => $total_entradas - $total_saidas
        ];

        return [
            'resumo' => $payments,
            'movs' => $movs,
            'dados' => $dados,
            // 'total_pago' => $total_pago,
        ];
    }

    public function listPayments($params)
    {
        $payments = Payment::where('empresa_id', $this->user->empresa_id)->get();

        return $payments;
    }

    public function getByIdPayment($id)
    {
        $payment = Payment::find($id);

        return $payment;
    }

    public function createPayment($data)
    {
        $data['empresa_id'] = $this->user->empresa_id;
        $payment = Payment::create($data);

        $payment->save();

        return $payment;
    }

    public function updatePayment($data, $id)
    {
        $payment = Payment::find($id);
        $resp = $payment->fill($data);

        $resp->save();

        return $resp;
    }

    public function deletePayments($id)
    {
        $payment = Payment::find($id);

        $resp = $payment->delete();

        return $resp;
    }
}
