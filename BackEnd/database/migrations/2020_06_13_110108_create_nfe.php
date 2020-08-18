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
            //empresa
            $table->unsignedInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade')->onUpdate('no action');

            //emitente
            $table->unsignedInteger('emitente_id')->nullable();
            $table->foreign('emitente_id')->references('id')->on('emitentes')->onDelete('no action')->onUpdate('no action');

            //venda
            $table->unsignedInteger('venda_id')->nullable();
            $table->foreign('venda_id')->references('id')->on('vendas')->onDelete('no action')->onUpdate('no action');

            //cliente
            $table->unsignedInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('no action')->onUpdate('no action');
            $table->string('razao')->nullable();
            $table->string('fantasia')->nullable();
            $table->string('cnpj', 15)->nullable();
            $table->string('inscricao_estadual', 20)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro')->nullable();
            $table->string('complemento')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('ibge', 20)->nullable();

            //transportadora
            $table->unsignedInteger('transportadora_id')->nullable();
            $table->string('transportadora')->nullable();
            $table->string('transp_cnpj', 15)->nullable();
            $table->string('transp_inscricao_estadual', 20)->nullable();
            $table->string('transp_uf', 2)->nullable();

            $table->decimal('transp_quantidade', 10, 4)->default(0);
            $table->string('transp_especie')->nullable();
            $table->decimal('transp_pesoBruto', 10, 4)->default(0);
            $table->decimal('transp_pesoLiquido', 10, 4)->default(0);

            $table->string('transp_placa', 10)->nullable();
            $table->string('transp_placaUF', 2)->nullable();

            //totais
            $table->decimal('subtotal', 10, 4)->default(0);
            $table->decimal('frete', 10, 4)->default(0);
            $table->decimal('desconto', 10, 4)->default(0);
            $table->decimal('total', 10, 4)->default(0);
            // $table->integer('tipo_pgto')->default(1);
            // $table->string('forma_pgto')->default('01');

            $table->string('natop', 255)->default('Venda');
            $table->integer('ind_pres')->default(1);
            $table->integer('ind_final')->default(0);
            $table->integer('mod_frete')->default(9);
            $table->integer('tipo_nf')->default(1);
            $table->integer('finalidade_nf')->default(1);
            $table->integer('serie');
            $table->integer('sequencia');
            $table->integer('tpamb');
            $table->integer('cstatus')->default(1);
            $table->string('status')->default('Aberta');
            $table->string('chave', 44);
            $table->string('chave_referencia', 44);
            $table->string('nota_referencia', 50);
            $table->string('recibo', 100)->nullable();
            $table->string('protocolo', 100)->nullable();
            $table->text('xjust')->nullable();
            $table->text('infor_adicional')->nullable();
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
