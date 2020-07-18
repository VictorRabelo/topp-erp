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
      //   Schema::create('contas_receber', function (Blueprint $table) {
      //       $table->id();
      //       $table->timestamps();
      //   });
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
