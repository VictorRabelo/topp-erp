<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsPaymentCaixa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('caixa', function (Blueprint $table) {
            $table->unsignedInteger('forma_id')->nullable()->after('venda_id');
            $table->foreign('forma_id')->references('id')->on('payments_forms')->onDelete('no action')->onUpdate('no action');

            $table->string('forma')->nullable()->after('forma_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
