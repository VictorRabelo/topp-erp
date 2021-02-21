<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Models\Product;
use App\User;

class FastRepositorie
{
    function __construct()
    {
    }

    public function pushCarga($params, $empresa_id)
    {
        if ($params['table'] == 'produtos') {
            $products = Product::where('empresa_id', $empresa_id)->get();

            return $products;
        }
        if ($params['table'] == 'payments_forms') {
            $products = Payment::where('empresa_id', $empresa_id)->get();

            return $products;
        }
    }
}
