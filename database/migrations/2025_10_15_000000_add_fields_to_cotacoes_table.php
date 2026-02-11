<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCotacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotacoes', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('status_envio');
            $table->text('observacao')->nullable()->after('descricao');
            $table->string('endereco_entrega')->nullable()->after('observacao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotacoes', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'observacao', 'endereco_entrega']);
        });
    }
}
