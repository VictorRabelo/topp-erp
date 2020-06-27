<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumEstoqueSaida extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table(
         'estoque_saida',
         function (Blueprint $table) {
            $table->unsignedInteger('venda_id')->nullable();
            $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('cascade')->onUpdate('no action');
         }
      );
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
