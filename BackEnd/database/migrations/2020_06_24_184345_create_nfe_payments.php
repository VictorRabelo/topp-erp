<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfePayments extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('nfe_payments', function (Blueprint $table) {
         $table->increments('id');
         $table->unsignedInteger('nfe_id');
         $table->foreign('nfe_id')->references('id')->on('nfe')->onDelete('cascade')->onUpdate('no action');

         $table->integer('forma_id');
         $table->text('forma');
         $table->decimal('valor', 10, 2);
         $table->text('obs');

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
      Schema::dropIfExists('nfe_payments');
   }
}
