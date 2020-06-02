<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendas extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('vendas', function (Blueprint $table) {
         $table->increments('id');

         $table->unsignedInteger('empresa_id');
         $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('no action');

         $table->unsignedInteger('user_id')->nullable();
         $table->foreign('user_id')->references('id')->on('users')->onDelete('no action')->onUpdate('no action');

         $table->unsignedInteger('cliente_id')->nullable();
         $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('no action')->onUpdate('no action');

         $table->string('cliente')->default('Consumidor Final');
         $table->string('cpf', 20)->nullable();

         $table->decimal('subtotal', 15, 4)->default(0);
         $table->decimal('desconto', 15, 4)->default(0);
         $table->decimal('total', 15, 4)->default(0);

         $table->integer('status')->default(1);
         // $table->integer('tipo')->default(1);

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
      Schema::dropIfExists('vendas');
   }
}
