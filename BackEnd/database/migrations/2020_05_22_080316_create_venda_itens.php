<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendaItens extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('venda_itens', function (Blueprint $table) {
         $table->increments('id');

         $table->unsignedInteger('venda_id');
         $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('cascade')->onUpdate('no action');

         $table->unsignedInteger('produto_id')->nullable();
         $table->foreign('produto_id')->references('id')->on('produtos')->onDelete('no action')->onUpdate('no action');

         $table->string('descricao')->nullable();

         $table->decimal('quantidade', 15, 4);
         $table->decimal('valor_unitario', 15, 4);
         $table->decimal('desconto', 15, 4);
         $table->decimal('total', 15, 4);

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
      Schema::dropIfExists('venda_itens');
   }
}
