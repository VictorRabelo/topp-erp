<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfeItens extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('nfe_itens', function (Blueprint $table) {
         $table->increments('id');

         $table->unsignedInteger('nfe_id');
         $table->foreign('nfe_id')->references('id')->on('nfe')->onDelete('cascade')->onUpdate('no action');

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
      Schema::dropIfExists('nfe_itens');
   }
}
