<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorFiscalEventos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitor_fiscal_eventos', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('emitente_id');
            $table->foreign('emitente_id')->references('id')->on('emitentes')->onDelete('cascade')->onUpdate('no action');

            $table->integer('sequencia');
            $table->integer('tpevento');
            $table->string('evento')->nullable();
            $table->text('xjust')->nullable();
            $table->string('chave', 44)->nullable();

            $table->string('nprot', 50)->nullable();
            $table->integer('cstatus')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monitor_fiscal_eventos');
    }
}
