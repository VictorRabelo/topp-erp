<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsPermissionsContas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_permissions', function (Blueprint $table) {
            //financeiro
            $table->integer('contas_receber')->default(0)->after('delete_caixa');
            $table->integer('contas_receber_create')->default(0)->after('contas_receber');
            $table->integer('contas_receber_edit')->default(0)->after('contas_receber_create');
            $table->integer('contas_receber_delete')->default(0)->after('contas_receber_edit');
            $table->integer('contas_receber_payment')->default(0)->after('contas_receber_delete');

            $table->integer('contas_pagar')->default(0)->after('contas_receber_payment');
            $table->integer('contas_pagar_create')->default(0)->after('contas_pagar');
            $table->integer('contas_pagar_edit')->default(0)->after('contas_pagar_create');
            $table->integer('contas_pagar_delete')->default(0)->after('contas_pagar_edit');
            $table->integer('contas_pagar_payment')->default(0)->after('contas_pagar_delete');
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
