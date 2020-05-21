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
         $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('no action');

         // $table->unsignedInteger('cliente_id');
         // $table->foreign('cliente_id')->references('id')->on('clientes');

         $table->string('codigo_barras')->nullable();
         $table->string('referencia')->nullable();
         $table->string('descricao');

         $table->decimal('custo', 15, 3)->default(0);
         $table->decimal('margem', 5, 2)->default(0);
         $table->decimal('preco', 15, 3)->default(0);
         $table->decimal('estoque', 15, 3)->default(0);

         $table->string('medida', 3)->default('UN');
         $table->integer('origin')->default(0);
         $table->string('ncm', 20)->nullable();
         $table->string('cfop', 4)->nullable();
         $table->string('cst', 5)->nullable();
         // $table->string('fci')->nullable();

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
