<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateItensCotacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itens_cotacao', function (Blueprint $table) {
            // Remove peso column
            if (Schema::hasColumn('itens_cotacao', 'peso')) {
                $table->dropColumn('peso');
            }

            // Change unidade column from varchar to tinyint
            $table->Integer('unidade')->change();

            // Add observacao column as nullable string
            if (!Schema::hasColumn('itens_cotacao', 'observacao')) {
                $table->string('observacao')->nullable()->after('valor');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itens_cotacao', function (Blueprint $table) {
            // Add peso column back
            if (!Schema::hasColumn('itens_cotacao', 'peso')) {
                $table->integer('peso')->after('quantidade');
            }

            // Change unidade column back to string
            $table->string('unidade')->change();

            // Drop observacao column
            if (Schema::hasColumn('itens_cotacao', 'observacao')) {
                $table->dropColumn('observacao');
            }
        });
    }
}
