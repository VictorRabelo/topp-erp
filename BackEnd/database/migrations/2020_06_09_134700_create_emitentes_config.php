<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmitentesConfig extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('emitentes_config', function (Blueprint $table) {
         $table->increments('id');
         $table->unsignedInteger('emitente_id');
         $table->foreign('emitente_id')->references('id')->on('emitentes')->onDelete('cascade')->onUpdate('no action');
         $table->integer('modelo');
         $table->integer('seq')->default(1);
         $table->integer('seqHomolog')->default(1);
         $table->integer('serie')->default(1);
         $table->integer('serieHomolog')->default(1);
         $table->integer('tpnf')->default(1);
         $table->integer('tpemiss')->default(1);
         $table->integer('tpimp')->default(1);
         $table->integer('tpamb')->default(1);
         $table->string('csc')->nullable();
         $table->string('cscHomolog')->nullable();
         $table->string('cscid')->nullable();
         $table->string('cscidHomolog')->nullable();
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
      Schema::dropIfExists('emitentes_config');
   }
}
