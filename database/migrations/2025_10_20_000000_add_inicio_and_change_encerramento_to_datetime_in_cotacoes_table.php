<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInicioAndChangeEncerramentoToDatetimeInCotacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotacoes', function (Blueprint $table) {
            $table->dateTime('inicio')->nullable()->after('numero');
            $table->dateTime('encerramento')->nullable()->change();
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
            $table->dropColumn('inicio');
            $table->date('encerramento')->change();
        });
    }
}
