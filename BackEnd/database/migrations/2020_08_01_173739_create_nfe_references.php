<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfeReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfe_references', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('nfe_id');
            $table->foreign('nfe_id')->references('id')->on('nfe')->onDelete('cascade')->onUpdate('no action');

            $table->string('numero_nfe', 20)->nullable();
            $table->string('chave_nfe', 44);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfe_references');
    }
}
