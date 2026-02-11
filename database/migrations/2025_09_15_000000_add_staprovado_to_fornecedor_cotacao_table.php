<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaprovadoToFornecedorCotacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fornecedor_cotacao', function (Blueprint $table) {
            $table->tinyInteger('staprovado')->default(0)->after('valor_total');
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
            $table->dropColumn('staprovado');
        });
    }
}
