<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreteObservacaoToPedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->tinyInteger('tipo_frete')->comment('1: CIF; 2: FOB')->after('status');
            $table->decimal('valor_frete', 15, 2)->nullable()->after('tipo_frete');
            $table->string('observacao')->nullable()->after('valor_frete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['tipo_frete', 'valor_frete', 'observacao']);
        });
    }
}
