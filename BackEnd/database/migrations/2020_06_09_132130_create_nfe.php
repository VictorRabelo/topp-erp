<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfe extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('nfe', function (Blueprint $table) {
         $table->integerIncrements('id');
         $table->unsignedInteger('empresa_id');
         $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('no action');
         $table->unsignedInteger('venda_id')->nullable();
         $table->integer('numero');
         $table->integer('tpamb');
         $table->integer('cstatus');
         $table->string('status');
         $table->string('chave', 44);
         $table->integer('recibo')->nullable();
         $table->integer('protocolo')->nullable();
         $table->text('xjust')->nullable();
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
      Schema::dropIfExists('nfe');
   }
}
