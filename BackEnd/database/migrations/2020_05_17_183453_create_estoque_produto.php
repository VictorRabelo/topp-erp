<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstoqueProduto extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      // Schema::create('estoque_produto', function (Blueprint $table) {
      //    $table->increments('id');
      //    $table->unsignedInteger('produto_id');
      //    $table->foreign('produto_id')->references('id')->on('produtos');

      //    $table->unsignedInteger('cliente_id');
      //    $table->foreign('cliente_id')->references('id')->on('clientes');

      //    $table->decimal('valor_unitario', 15, 4);
      //    $table->decimal('quantidade', 15, 4);
      //    $table->integer('nota')->nullable();

      //    $table->timestamps();
      // });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('estoque_produto');
   }
}
