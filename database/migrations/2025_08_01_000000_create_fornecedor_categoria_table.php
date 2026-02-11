<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFornecedorCategoriaTable extends Migration
{
    public function up()
    {
        Schema::create('categoria_fornecedor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fornecedor_id');
            $table->unsignedBigInteger('categoria_id');
            $table->timestamps();

            $table->foreign('fornecedor_id')->references('id')->on('fornecedores')->onDelete('cascade');
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('cascade');

            $table->unique(['fornecedor_id', 'categoria_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categoria_fornecedor');
    }
}
