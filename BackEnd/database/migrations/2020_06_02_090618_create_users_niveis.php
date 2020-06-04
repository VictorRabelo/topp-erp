<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersNiveis extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('users_permissions', function (Blueprint $table) {
         $table->integerIncrements('id');
         $table->unsignedInteger('empresa_id');
         $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('no action');

         $table->string('descricao');

         //usuarios
         $table->integer('users')->default(0);
         $table->integer('create_user')->default(0);
         $table->integer('edit_user')->default(0);
         $table->integer('delete_user')->default(0);

         //produtos
         $table->integer('products')->default(0);
         $table->integer('create_product')->default(0);
         $table->integer('edit_product')->default(0);
         $table->integer('delete_product')->default(0);

         //clientes
         $table->integer('clients')->default(0);
         $table->integer('create_client')->default(0);
         $table->integer('edit_client')->default(0);
         $table->integer('delete_client')->default(0);

         //vendas
         $table->integer('vendas')->default(0);
         $table->integer('create_venda')->default(0);
         $table->integer('edit_venda')->default(0);
         $table->integer('delete_venda')->default(0);
         $table->integer('cancel_venda')->default(0);
         $table->integer('finalizar_venda')->default(0);

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
      Schema::dropIfExists('users_niveis');
   }
}
