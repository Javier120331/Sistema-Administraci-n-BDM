<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignLicenciasMovAsis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientos_asistencias', function (Blueprint $table) {
            $table->integer("licencia_id")->unsigned()->nullable();
            $table->foreign("licencia_id")
              ->references("id")
              ->on("licencias")
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
            $table->dropForeign("movimientos_asistencias_licencia_id_foreign");
            $table->dropColumn('licencia_id');
        });
    }
}
