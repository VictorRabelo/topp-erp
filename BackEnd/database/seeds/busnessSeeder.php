<?php

use App\Models\Register;
use Illuminate\Database\Seeder;

class busnessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new Register();
        $data->id = 1;
        $data->tipo = 2;
        $data->razao = 'SIG Automação';
        $data->plano = 1;
        $data->licenca = date('Y-m-d', strtotime("+ 1 month"));
        $data->save();
    }
}
