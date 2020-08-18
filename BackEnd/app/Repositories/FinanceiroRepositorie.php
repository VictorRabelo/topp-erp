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
        if (isset($params['data']) && !empty($params['data'])) {
            $data = $params['data'];
        } else {
            $data = $this->dateNow;
        }

        $movs = Caixa::where('updated_at', 'like', '%' . $data . '%')->get();

        $total_entradas = 0;
        $total_saidas = 0;
        foreach ($movs as $mov) {
            if ($mov->tipo == 1) { //entrada
                $total_entradas += $mov->valor;
            } else { //saida
                $total_saidas += $mov->valor;
            }
        }

        $payments = DB::table('caixa')
            ->select(
                DB::raw('SUM(caixa.valor) as total'),
                'caixa.*'
            )
            ->where('caixa.updated_at', 'like', '%' . $data . '%')
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
