<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsNfeItens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfe_itens', function (Blueprint $table) {
            $table->string('cfop', 4)->default('5102')->after('total');
            $table->string('cst_icms', 4)->default('102')->after('cfop');
            $table->float('p_icms', 15, 4)->after('cst_icms');
            $table->string('cst_ipi', 4)->default('52')->after('p_icms');
            $table->float('p_ipi', 15, 4)->after('cst_ipi');
            $table->string('cst_pis', 4)->default('07')->after('p_ipi');
            $table->float('p_pis', 15, 4)->after('cst_pis');
            $table->string('cst_cofins', 4)->default('07')->after('p_pis');
            $table->float('p_cofins', 15, 4)->after('cst_cofins');
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
