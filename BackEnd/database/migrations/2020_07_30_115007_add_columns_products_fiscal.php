<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsProductsFiscal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->renameColumn('cst', 'cst_icms');
            $table->string('cst', 4)->default('102')->change();
            $table->float('p_icms', 10, 3)->default(0)->after('cst');

            $table->string('cst_ipi', 4)->default('52')->after('p_icms');
            $table->float('p_ipi', 10, 3)->default(0)->after('cst_ipi');

            $table->string('cst_pis', 4)->default('07')->after('p_ipi');
            $table->float('p_pis', 10, 3)->default(0)->after('cst_pis');

            $table->string('cst_cofins', 4)->default('07')->after('p_pis');
            $table->float('p_cofins', 10, 3)->default(0)->after('cst_cofins');
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
