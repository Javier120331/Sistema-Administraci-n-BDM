<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVacacionesProgreQuitarDias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vacaciones_progresivas', function (Blueprint $table) {
            $table->dropColumn('cantidad_dias_adicionales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vacaciones_progresivas', function (Blueprint $table) {
            $table->integer('cantidad_dias_adicionales');
        });
    }
}
