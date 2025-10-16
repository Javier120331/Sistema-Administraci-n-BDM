<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMovExpAddDescargableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientos_asistencias_exportaciones', function (Blueprint $table) {
            $table->tinyInteger('descargable')->default(0);
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
            $table->dropColumn('descargable');
        });
    }
}
