<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsAddCompradorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Add comprador_id foreign key column
            $table->unsignedBigInteger('comprador_id')->nullable()->after('user_id');

            $table->foreign('comprador_id')->references('id')->on('users')->onDelete('set null');
        });

        // Drop the pivot table produto_user_comprador
        Schema::dropIfExists('produto_user_comprador');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate the pivot table
        Schema::create('produto_user_comprador', function (Blueprint $table) {
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('produto_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->primary(['produto_id', 'user_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['comprador_id']);
            $table->dropColumn('comprador_id');
        });
    }
}
