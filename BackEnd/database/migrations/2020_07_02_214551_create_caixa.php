<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaixa extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('caixa', function (Blueprint $table) {
         $table->increments('id');

         $table->unsignedInteger('empresa_id');
         $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('no action');

         $table->unsignedInteger('cliente_id')->nullable();
         $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('no action')->onUpdate('no action');

         $table->unsignedInteger('venda_id')->nullable();
         $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('no action')->onUpdate('no action');

         $table->integer('tipo')->nullable();
         $table->string('descricao')->nullable();

         $table->decimal('valor', 15, 4);

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
      Schema::dropIfExists('caixa');
   }
}
