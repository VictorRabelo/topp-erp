<?php

namespace App\Tools;

use NFePHP\POS\PrintConnectors\Base64PrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;

class Util
{


    public function geraNewVencimento($currentData, $more = 0)
    {
        // return $currentData;
        $dia = date('d', strtotime($currentData));
        $mes = date('m', strtotime($currentData));
        $ano = date('Y', strtotime($currentData));

        if ($more > 0) {
            $mes += $more;
        }

        if ($mes > 12) { //se passar do mês 12, então inicia com o $mes 1 do próximo ano
            $mes = 1;
            $ano = $ano + 1;
            echo "ano maior que 12";
        }

        if ($mes == 4 && $dia > 30 || $mes == 6 && $dia > 30 || $mes == 9 && $dia > 30 || $mes == 11 && $dia > 30) {
            $dia = 30;
        } else if ($mes == 2 && $dia > 28) {
            $dia = 28;
        }

        // return date('Y-m-d', strtotime("$ano-$mes-$dia"));
    }

    public function printSaleCupom(object $business, object $sale, object $extras)
    {
        //armazena todo o codigo para impressão
        $connector = new Base64PrintConnector();

        $printer = new Printer($connector);

        /* Initialize */
        $printer->initialize();


        //header ================================================================================================
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        if (isset($business->logo) && ($business->logo != null && $business->logo != '')) {
            $logopath = EscposImage::load(storage_path("app/public/{$business->logo}"));
            $printer->bitImage($logopath);
        }

        $printer->setEmphasis(true);
        $printer->text($business->fantasia . "\n");
        $printer->setEmphasis(false);

        $printer->setFont(Printer::FONT_B);
        $printer->text((strlen($business->cnpj) == 11) ? $this->mask($business->cnpj, '###.###.###-##') :
            $this->mask($business->cnpj, '##.###.###/####-##') . "\n");

        $printer->text($business->logra . ', ' . $business->numero . ' - ' . $business->bairro . "\n");
        $printer->text($business->cidade . '/' . $business->uf . ' - ' . $this->mask($business->cep, '##.###-###') . "\n\n\n");

        $printer->setFont(Printer::FONT_A);

        //Fim header ================================================================================================


        //sub Header ================================================================================================
        $printer->setEmphasis(true);
        $printer->text("Cupom não fiscal \n");
        $printer->setEmphasis(false);

        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $line = str_pad("Cupom:" . $sale->id, 24, " ", STR_PAD_RIGHT);
        $line .= str_pad("Data:" . date('d/m/Y', strtotime($sale->created_at)), 24, " ", STR_PAD_LEFT);

        $printer->text($line);
        //Fim sub Header ================================================================================================

        $printer->text($this->separador());

        //Itens do cupom ================================================================================================
        $printer->setEmphasis(true);
        $line = str_pad(strtoupper("cod"), 7, " ", STR_PAD_RIGHT);
        $line .= str_pad(strtoupper("descricao"), 18, " ", STR_PAD_RIGHT);
        $line .= str_pad(strtoupper("Qtd."), 9, " ", STR_PAD_RIGHT);
        $line .= str_pad(strtoupper("Unit."), 9, " ", STR_PAD_RIGHT);
        $line .= str_pad(strtoupper("Total"), 9, " ", STR_PAD_RIGHT);

        $printer->text($line . "\n");
        $printer->setEmphasis(false);

        //sql Itens
        $count_itens = 0;
        $vdesc_itens = 0;
        $total_venda = 0;
        foreach ($sale->itens as $item) {
            $cod = $item->produto['codigo_barras'];
            $descricao = substr($item->descricao, 0, 41);
            $quantidade = number_format($item->quantidade, 3, ',', '.');
            $valor_unitario = number_format($item->valor_unitario, 2, ',', '.');
            $total = number_format($item->valor_unitario * $item->quantidade, 2, ',', '.');

            //somas externas
            $count_itens++;
            $vdesc_itens += $item->desconto;
            $total_venda += $item->valor_unitario * $item->quantidade;

            $line = str_pad(strtoupper($cod), 7, " ", STR_PAD_RIGHT);
            $line .= str_pad(strtoupper($descricao), 41, " ", STR_PAD_RIGHT);
            $printer->text($line . "\n");

            $line = str_pad(strtoupper($quantidade), 28, " ", STR_PAD_LEFT);
            $line .= str_pad(strtoupper($valor_unitario), 10, " ", STR_PAD_LEFT);
            $line .= str_pad(strtoupper($total), 10, " ", STR_PAD_LEFT);
            $printer->text($line . "\n");
        }

        $total_final = $total_venda - $sale->desconto - $vdesc_itens;

        $printer->text($this->separador());

        //Fim Itens do cupom ================================================================================================

        //Pagamento do cupom ================================================================================================

        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->setEmphasis(true);

        $line = str_pad("Itens: ", 30, " ", STR_PAD_LEFT);
        $line .= str_pad($count_itens, 18, " ", STR_PAD_LEFT);
        $printer->text($line . "\n");

        $line = str_pad("subtotal: ", 30, " ", STR_PAD_LEFT);
        $line .= str_pad("R$ " . number_format($total_venda, 2, ',', '.'), 18, " ", STR_PAD_LEFT);
        $printer->text($line . "\n");

        $line = str_pad("Descontos: ", 30, " ", STR_PAD_LEFT);
        $line .= str_pad("R$ " . number_format($sale->desconto + $vdesc_itens, 2, ',', '.'), 18, " ", STR_PAD_LEFT);
        $printer->text($line . "\n");

        $line = str_pad("Total: ", 30, " ", STR_PAD_LEFT);
        $line .= str_pad("R$ " . number_format($total_final, 2, ',', '.'), 18, " ", STR_PAD_LEFT);
        $printer->text($line . "\n");



        $printer->setEmphasis(false);

        $printer->text($this->separador('-'));

        //sql Payments
        $total_pago = 0;
        foreach ($sale->payments as $item) {
            $forma = substr($item->forma, 0, 41);
            $valor = number_format($item->valor, 2, ',', '.');

            //somas externas
            $total_pago += $item->valor;

            $line = str_pad(strtoupper($forma) . ":", 33, " ", STR_PAD_LEFT);
            $line .= str_pad("R$ " . strtoupper($valor), 15, " ", STR_PAD_LEFT);
            $printer->text($line . "\n");
        }

        $printer->setEmphasis(true);

        $printer->text($this->separador('-'));

        $troco = $total_pago - $total_final;

        $line = str_pad("Troco:", 33, " ", STR_PAD_LEFT);
        $line .= str_pad("R$ $troco", 15, " ", STR_PAD_LEFT);
        $printer->text($line . "\n");

        $printer->setEmphasis(false);

        $printer->setJustification(Printer::JUSTIFY_LEFT);

        //Fim Pagamento do cupom ================================================================================================

        $printer->text("Operador: " . strtoupper($extras->vendedor) . "\n");

        $printer->text($this->separador('-'));

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setFont(Printer::FONT_B);
        $printer->text("Powered by TOPP ERP\n");
        $printer->setFont(Printer::FONT_A);

        // for ($i=0; $i < 5; $i++) {
        $printer->feed();
        // }

        $printer->cut();
        $printer->close();

        // Obter impressão em base64
        $base64 = $connector->getBase64Data();

        // Retornar resposta
        return $base64;
    }

    public function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    public function separador($str = '=')
    {
        return str_repeat($str, 48) . "\n";
    }
}
