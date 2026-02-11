<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCompradorIdToCnpjCompradorInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['comprador_id']);
            // Rename column
            $table->renameColumn('comprador_id', 'cnpj_comprador');
            // Re-add foreign key constraint with new column name
            $table->foreign('cnpj_comprador')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['cnpj_comprador']);
            $table->renameColumn('cnpj_comprador', 'comprador_id');
            $table->foreign('comprador_id')->references('id')->on('users')->onDelete('set null');
        });
    }
}
