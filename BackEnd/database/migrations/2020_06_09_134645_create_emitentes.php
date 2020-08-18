<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmitentes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emitentes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('no action');
            $table->integer('tipo')->default(1);
            $table->integer('crt')->default(1);
            $table->string('razao')->nullable();
            $table->string('fantasia')->nullable();
            $table->string('cnpj', 20)->nullable();
            $table->string('inscricao_estadual', 20)->nullable();
            $table->string('inscricao_municipal', 20)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->text('email')->nullable();
            $table->text('email_contador')->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('logradouro', 150)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 150)->nullable();
            $table->string('complemento', 150)->nullable();
            $table->string('cidade', 200)->nullable();
            $table->string('ibge', 20)->nullable();
            $table->string('uf', 2)->nullable();
            $table->text('file_pfx')->nullable();
            $table->string('senha_pfx', 255)->nullable();
            $table->text('logo')->nullable();
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
        Schema::dropIfExists('emitentes');
    }
}
