<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMovimientosExportacionesAreaNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientos_asistencias_exportaciones', function (Blueprint $table) {
            $table->string('area')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimientos_asistencias_exportaciones', function (Blueprint $table) {
              $table->string('area')->nullable(false)->change();
        });
    }
}
