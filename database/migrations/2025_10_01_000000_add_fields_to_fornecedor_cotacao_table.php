<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToFornecedorCotacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fornecedor_cotacao', function (Blueprint $table) {
            $table->tinyInteger('tipo_frete')->default(1)->comment('1: CIF, 2: FOB');
            $table->decimal('valor_frete', 15, 2)->nullable();
            $table->decimal('faturamento_minimo', 15, 2)->nullable();
            $table->string('observacao')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fornecedor_cotacao', function (Blueprint $table) {
            $table->dropColumn(['tipo_frete', 'valor_frete', 'faturamento_minimo', 'observacao']);
        });
    }
}
