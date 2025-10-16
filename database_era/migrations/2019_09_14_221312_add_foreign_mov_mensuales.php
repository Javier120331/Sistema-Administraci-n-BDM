<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Agrega clave foranea a movimientos_mensuales
 * por algún motivo no fue agregado al crear la migración
 * original de movimientos_mensuales
 * @date 2019-09-14
 */
class AddForeignMovMensuales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientos_mensuales', function (Blueprint $table) {
            DB::raw("ALTER TABLE movimientos_mensuales ENGINE='InnoDB'");
            $table->foreign('cosechador_id')
              ->references('id')
              ->on('cosechadores')
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
        Schema::table('movimientos_mensuales', function (Blueprint $table) {
            $table->dropForeign('cosechador_id');
        });
    }
}
