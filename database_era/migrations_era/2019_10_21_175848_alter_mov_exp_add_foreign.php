<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMovExpAddForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientos_asistencias', function (Blueprint $table) {
          $table->integer("exportacion_id")->unsigned()->nullable();
          $table->foreign("exportacion_id")
            ->references("id")
            ->on("movimientos_asistencias_exportaciones")
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimientos_asistencias', function (Blueprint $table) {
          $table
            ->dropForeign("movimientos_asistencias_movimientos_asistencias_exportaciones_id_foreign");
          $table->dropColumn('exportacion_id');
        });
    }
}
