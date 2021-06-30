<?php

namespace App\Repositories;

use App\Models\Caixa;
use App\Models\Client;
use App\Models\ContasReceber;
use App\Models\Empresa;
use App\Models\EstoqueSaida;
use App\Models\NFCe;
use App\Models\NFe;
use App\Models\NfeItens;
use App\Models\NfePayment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Venda;
use App\Models\VendaItens;
use App\Models\VendasPayments;
use App\Tools\Util;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendaRepositorie
{
    private $model;

    function __construct()
    {
        $this->model = new Venda();
        $this->user = Auth::guard('api')->user();
    }

    public function list($params)
    {
        $query = $this->model->where('empresa_id', $this->user->empresa_id)->orderBy('created_at', 'DESC')->get();

        // $query = $this->order_list($query);

        return $query;
    }


    public function novo($post)
    {
        $insert = $this->model;

        $dados['empresa_id'] = $this->user->empresa_id;
        $dados['user_id'] = $this->user->id;

        return $insert->create($dados);
    }

    public function getSingle(int $id)
    {
        $dados = $this->model->where('id', $id)->where('empresa_id', $this->user->empresa_id)->first();

        if (empty($dados)) {
            return array('erro' => 'Venda não localizada!');
        }

        if (!empty($dados->cliente_id)) {
            $client = Client::find($dados->cliente_id);
            $dados->cliente = (isset($client->razao)) ? $client->razao : "Consumidor Final";
        }

        $vendedor = User::find($dados->user_id);
        $dados->vendedor = (isset($vendedor->nome)) ? $vendedor->nome : "";

        $nfce = NFCe::where('venda_id', $id)->where('cstatus', 100)->orderBy('created_at', 'desc')->first();
        $dados->nfce = $nfce;

        $nfe = NFe::where('venda_id', $id)->where('cstatus', 100)->orderBy('created_at', 'desc')->first();
        $dados->nfe = $nfe;

        // $dados = $this->return_padrao($dados);

        return $dados;
    }

    public function editar($post, int $id)
    {
        $venda = $post['vendaCurrent'];
        $payments = $post['paymentsCurrent'];

        $_gen_payments = $this->gen_payments($payments, $venda);

        if (!$_gen_payments) {
            return $_gen_payments;
        }

        $_finish_venda = $this->finish_venda($venda);

        return $_finish_venda;
    }

    private function finish_venda(array $dados)
    {
        // print_r($dados);
        // return $dados;
        $venda = $this->model->find($dados['id']);
        $venda->status = 10;
        $venda->desconto = $dados['desconto_b'];
        $venda->total = $dados['total'];

        $resp = $venda->save();

        if ($resp) {
            $this->move_estoque_saida($venda->id);
            // $this->geraCaixa($venda);
        }

        return $resp;
    }
    private function gen_payments(array $payments, array $venda)
    {
        foreach ($payments as $payment) {
            $data = [
                'venda_id' => $venda['id'],
                'forma_id' => $payment['id'],
                'forma' => $payment['forma'],
                'valor' => $payment['valor'],
                // 'troco' => ($dados->more > 0) ? $troco : 0,
                'resto' => $payment['resto'],
                'obs' => (isset($payment['obs'])) ? $payment['obs'] : null
            ];

            $pagamento = new VendasPayments();
            $pagamento->fill($data);

            if (!$pagamento->save()) {
                VendasPayments::where('venda_id', $venda['id'])->delete();
                return false;
            }


            if (isset($payment['parcelas'])) {
                foreach ($payment['parcelas'] as $item) {
                    $conta = [
                        'empresa_id' => $this->user->empresa_id,
                        'venda_id' => $venda['id'],
                        'cliente_id' => $venda['cliente_id'],
                        'cliente' => $venda['cliente'],
                        'vendedor_id' => $venda['user_id'],
                        'vendedor' => $venda['vendedor'],
                        'descricao' => "Parcela da venda Nº: " . $venda['id'],
                        'documento' => $venda['id'] . "/" . $item['num'],
                        'valor' => $item['valor'],
                        'vencimento' => $item['data_vence']
                    ];

                    if (isset($payment['status_pago']) && $payment['status_pago'] == 10) {
                        $conta['situacao'] = 10;
                        $conta['valor_pago'] = $item['valor'];
                        $conta['data_pago'] = $item['data_vence'];

                        $this->geraCaixa($venda, $payment);
                    } else {
                        ContasReceber::create($conta);
                    }
                    
                }
            } else {
                $this->geraCaixa($venda, $payment);
            }
        }

        return true;
    }
    private function geraCaixa($venda, $payment)
    {
        $caixa = new Caixa();
        $caixa->empresa_id = $this->user->empresa_id;
        $caixa->cliente_id = $venda['cliente_id'];
        $caixa->venda_id = $venda['id'];
        $caixa->forma_id = $payment['id'];
        $caixa->forma = $payment['forma'];
        $caixa->tipo = 1;
        $caixa->descricao = "Ref. Venda n° {$caixa->venda_id} Realizada no balcão - {$caixa->forma}";
        $caixa->valor = (floatval($payment['resto']) > $payment['valor']) ? $payment['valor'] : floatval($payment['resto']);
        if (isset($payment['data_pago'])) {
            $caixa->data = $payment['data_pago'];
        }
        $caixa->save();
    }
    private function move_estoque_saida($venda_id)
    {
        $itens = VendaItens::where('venda_id', $venda_id)->get();

        foreach ($itens as $item) {
            $dados = array(
                'venda_id' => $item['venda_id'],
                'produto_id' => $item['produto_id'],
                'valor_unitario' => $item['valor_unitario'],
                'quantidade' => $item['quantidade']
            );

            $mov = EstoqueSaida::create($dados);

            $produto = Product::find($item['produto_id']);
            $produto->estoque -= $item['quantidade'];
            $produto->save();
        }
    }
    public function estorno_venda(int $id)
    {
        $venda = Venda::where('id', $id)->where('empresa_id', $this->user->empresa_id)->first();
        if (empty($venda)) {
            return array('erro' => ['message' => 'Venda não localizada.']);
        }

        $payments = VendasPayments::where('venda_id', $venda->id)->get();
        foreach ($payments as $payment) { //gerar caixa
            $caixa = Caixa::create([
                'empresa_id' => $venda->empresa_id,
                'cliente_id' => $venda->cliente_id,
                'venda_id' => $venda->venda_id,
                'forma_id' => $payment->forma_id,
                'forma' => $payment->forma,
                'tipo' => 2, //saida
                'descricao' => "Estorno da venda: {$venda->venda_id}, realizada no balcão - {$payment->forma}",
                'valor' => $payment->resto,
            ]);

            if (!$caixa->save()) {
                Caixa::where('venda_id', $venda->venda_id)->where('tipo', 2)->delete();
                return array('erro' => ['message' => "Falha no caixa ao estornar venda."]);
                break;
            }
        }

        $venda->status = 0;
        if (!$venda->save()) {
            Caixa::where('venda_id', $venda->venda_id)->where('tipo', 2)->delete();
            return array('erro' => ['message' => "Falha ao estornar venda."]);
        }

        return true;
    }

    public function delete(int $id)
    {
        $dados = $this->model->find($id);
        return $dados->delete();
    }

    public function cancel($id)
    {
        $dados = $this->model->find($id);
        $dados->status = 0;
        $dados->save();

        return $dados;
    }

    public function set_cliente($post, int $venda_id)
    {
        $venda = $this->model->find($venda_id);
        $resp = $venda->fill($post)->save();

        return $resp;
    }

    public function geraNFe(array $data)
    {
        $venda = $this->model->find($data['id']);
        $client = Client::find($venda->cliente_id);
        $itens = VendaItens::where('venda_id', $venda->id)->get();
        $payments = DB::table('vendas_payments')->where('venda_id', $venda->id)->get();

        $nfe = new NFe;

        $nfe->empresa_id = $venda->empresa_id;
        $nfe->venda_id = $venda->id;

        if (!empty($client)) {
            $nfe->cliente_id = $client->id;
            $nfe->razao = $client->razao;
            $nfe->fantasia = $client->fantasia;
            $nfe->cnpj = $client->cnpj;
            $nfe->inscricao_estadual = $client->inscricao_estadual;
            $nfe->logradouro = $client->logradouro;
            $nfe->numero = $client->numero;
            $nfe->bairro = $client->bairro;
            $nfe->complemento = $client->complemento;
            $nfe->cidade = $client->cidade;
            $nfe->uf = $client->uf;
            $nfe->ibge = $client->ibge;
        }

        $nfe->subtotal = $venda->subtotal;
        $nfe->desconto = $venda->desconto;
        $nfe->total = $venda->total;

        $nfe->status = "Aberta";
        $nfe->cstatus = 1;

        $nfe->save();

        if ($nfe->id > 0) {
            foreach ($itens as $item) {
                $array['nfe_id'] = $nfe->id;
                $array['produto_id'] = $item->produto_id;
                $array['descricao'] = $item->descricao;
                $array['quantidade'] = $item->quantidade;
                $array['valor_unitario'] = $item->valor_unitario;
                $array['desconto'] = $item->desconto;
                $array['total'] = $item->total;

                NfeItens::create($array);
            }
            foreach ($payments as $payment) {
                $array['nfe_id'] = $nfe->id;
                $array['forma_id'] = $payment->forma_id;
                $array['forma'] = $payment->forma;
                $array['valor'] = $payment->valor;
                $array['obs'] = $payment->obs;
                NfePayment::create($array);
            }
        }

        return array("nfe_id" => $nfe->id);
    }

    public function printSale($id)
    {
        $sale = $this->model->find($id);
        $sale->itens = VendaItens::with('produto')->where('venda_id', $id)->get();
        $sale->payments = VendasPayments::where('venda_id', $id)->get();

        $business = Empresa::find($sale->empresa_id);
        $user = User::find($sale->user_id);

        $extras = (object) [
            'vendedor' => $user->nome
        ];

        $tools = new Util();

        $cupom = $tools->printSaleCupom($business, $sale, $extras);

        return $cupom;
    }
}
