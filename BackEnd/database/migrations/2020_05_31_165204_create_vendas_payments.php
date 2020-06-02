<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendasPayments extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('vendas_payments', function (Blueprint $table) {
         $table->increments('id');
         $table->unsignedInteger('venda_id');
         $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('cascade')->onUpdate('no action');

         $table->unsignedInteger('forma_id');

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
      Schema::dropIfExists('vendas_payments');
   }
}
