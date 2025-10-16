<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignVacacionesMovAsis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientos_asistencias', function (Blueprint $table) {
          $table->integer("vacaciones_id")->unsigned()->nullable();
          $table->foreign("vacaciones_id")
            ->references("id")
            ->on("vacaciones")
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
          $table->dropForeign("movimientos_asistencias_vacaciones_id_foreign");
          $table->dropColumn('vacaciones_id');
        });
    }
}
