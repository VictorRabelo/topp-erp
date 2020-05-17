<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduct extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('produtos', function (Blueprint $table) {

         $table->increments('id');
         $table->unsignedInteger('empresa_id');
         $table->foreign('empresa_id')->references('id')->on('empresas');

         $table->string('codigo_barras')->default(1);
         $table->string('referencia')->nullable();
         $table->string('nome');

         $table->float('custo', 15, 3)->default(0);
         $table->float('margem', 5, 2)->default(0);
         $table->float('preco', 15, 3)->default(0);
         $table->float('estoque', 15, 3)->default(0);

         $table->integer('origin')->default(0);
         $table->string('ncm', 20)->nullable();
         $table->string('cfop', 4)->nullable();
         $table->string('cst', 5)->nullable();

         $table->text('foto')->nullable();

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
      Schema::dropIfExists('produtos');
   }
}
