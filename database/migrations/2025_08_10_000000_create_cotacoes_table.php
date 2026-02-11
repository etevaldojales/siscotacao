<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotacoes', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('id_usuario')->nullable();
            $table->bigInteger('numero')->unique();
            $table->date('encerramento');
            $table->tinyInteger('status')->default(1); // 1: Em aberto; 2: Programado; 3: Encerrado; 4: Cancelado; 5: Finalizado
            $table->tinyInteger('status_envio')->default(1); // 1: Não enviada; 2: Enviada
            $table->timestamps();

            $table->foreign('id_usuario')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotacoes');
    }
}
