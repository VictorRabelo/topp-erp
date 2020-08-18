<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsContas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contas_pagar', function (Blueprint $table) {
            $table->integer('situacao')->default(1)->after('historico');

            $table->float('juros', 10, 2)->default(0)->after('situacao');
            $table->float('desconto', 10, 2)->default(0)->after('juros');
            $table->float('acrescimo', 10, 2)->default(0)->after('desconto');

            $table->float('valor_pago', 10, 2)->default(0)->after('acrescimo');
            $table->dateTime('data_pago')->nullable()->after('valor_pago');
        });

        Schema::table('contas_receber', function (Blueprint $table) {
            $table->integer('situacao')->default(1)->after('historico');

            $table->float('juros', 10, 2)->default(0)->after('situacao');
            $table->float('desconto', 10, 2)->default(0)->after('juros');
            $table->float('acrescimo', 10, 2)->default(0)->after('desconto');

            $table->float('valor_pago', 10, 2)->default(0)->after('acrescimo');
            $table->dateTime('data_pago')->nullable()->after('valor_pago');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
