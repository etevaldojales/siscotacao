<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeysInJustificativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('justificativas', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['cotacao_id']);
            $table->dropForeign(['item_id']);

            // Add new foreign keys referencing correct tables
            $table->foreign('cotacao_id')->references('id')->on('cotacoes')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('itens_cotacao')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('justificativas', function (Blueprint $table) {
            // Drop new foreign keys
            $table->dropForeign(['cotacao_id']);
            $table->dropForeign(['item_id']);

            // Restore old foreign keys referencing fornecedor_cotacao
            $table->foreign('cotacao_id')->references('id')->on('fornecedor_cotacao')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('fornecedor_cotacao')->onDelete('cascade');
        });
    }
}
