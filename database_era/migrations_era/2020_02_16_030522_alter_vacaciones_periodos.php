<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVacacionesPeriodos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vacaciones', function (Blueprint $table) {
            $table->dropForeign('vacaciones_empleado_id_foreign');
            $table->dropColumn('empleado_id');
            $table->integer('periodo_id')->unsigned();
            $table->foreign('periodo_id')
              ->references('id')
              ->on('periodos')
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
        Schema::table('vacaciones', function (Blueprint $table) {
            $table->dropForeign('vacaciones_periodo_id_foreign');
            $table->dropColumn('periodo_id');
            $table->integer('empleado_id')->unsigned();
            $table->foreign('empleado_id')
              ->references('id')
              ->on('empleados')
              ->onDelete('cascade');
        });
    }
}
