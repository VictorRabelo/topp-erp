<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitorFiscal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitor_fiscal', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('emitente_id');
            $table->foreign('emitente_id')->references('id')->on('emitentes')->onDelete('cascade')->onUpdate('no action');

            $table->integer('nsu');
            $table->integer('numero_nfe');
            $table->string('razao')->nullable();
            $table->string('cnpj', 20)->nullable();
            $table->integer('tpnf')->nullable();
            $table->float('valor', 10, 2)->nullable();
            $table->string('chave', 44)->nullable();
            $table->string('nprot', 50)->nullable();
            $table->integer('cstatus')->nullable();
            $table->string('status')->nullable();
            $table->integer('csituacao')->nullable();

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
        Schema::dropIfExists('monitor_fiscal');
    }
}
