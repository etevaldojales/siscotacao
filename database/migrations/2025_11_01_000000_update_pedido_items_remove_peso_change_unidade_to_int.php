<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePedidoItemsRemovePesoChangeUnidadeToInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_items', 'peso')) {
                $table->dropColumn('peso');
            }
            // Change 'unidade' from string to integer
            $table->integer('unidade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedido_items', function (Blueprint $table) {
            $table->string('unidade', 255)->change();
            $table->integer('peso')->nullable();
        });
    }
}
