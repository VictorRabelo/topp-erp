<?php

namespace App\Tools;

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
}
