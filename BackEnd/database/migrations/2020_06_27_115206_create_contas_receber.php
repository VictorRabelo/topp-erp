<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContasReceber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contas_receber', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('no action');

            $table->unsignedInteger('categoria_id')->nullable();
            $table->unsignedInteger('venda_id')->nullable();

            $table->unsignedInteger('cliente_id');
            $table->string('cliente')->nullable();

            $table->unsignedInteger('vendedor_id');
            $table->string('vendedor')->nullable();

            $table->string('descricao')->nullable();
            $table->string('documento')->nullable();

            $table->float('valor', 10, 2)->default(0);
            $table->date('vencimento');

            $table->text('historico')->nullable();

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
        Schema::dropIfExists('contas_receber');
    }
}
