<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFornecedorCotacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fornecedor_cotacao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotacao_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('fornecedor_id');
            $table->decimal('valor_unitario', 15, 2);
            $table->decimal('valor_total', 15, 2);
            $table->timestamps();
            
            $table->foreign('cotacao_id')->references('id')->on('cotacoes');
            $table->foreign('item_id')->references('id')->on('itens_cotacao');
            $table->foreign('fornecedor_id')->references('id')->on('fornecedores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fornecedor_cotacao');
    }
}
