<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateJustificativasAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('justificativas', function (Blueprint $table) {
            // Drop the descricao column
            $table->dropColumn('descricao');

            // Add justificativa_id foreign key column
            $table->unsignedBigInteger('justificativa_id')->nullable()->after('valor_unitario');

            $table->foreign('justificativa_id')->references('id')->on('justificativa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('justificativas', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['justificativa_id']);
            $table->dropColumn('justificativa_id');

            // Add descricao column back
            $table->string('descricao')->after('valor_unitario');
        });
    }
}
