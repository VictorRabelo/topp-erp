<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContasPagarPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('contas_pagar_payment', function (Blueprint $table) {
        //     $table->increments('id');

        //     $table->unsignedInteger('conta_id');
        //     $table->foreign('conta_id')->references('id')->on('contas_pagar')->onDelete('cascade')->onUpdate('no action');

        //     $table->unsignedInteger('forma_id');
        //     $table->foreign('forma_id')->references('id')->on('payments_forms')->onDelete('no action')->onUpdate('no action');

        //     $table->text('forma');
        //     $table->float('valor', 10, 2)->default(0);
        //     $table->float('resto', 10, 2)->default(0);
        //     $table->text('obs');

        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contas_pagar_payment');
    }
}
