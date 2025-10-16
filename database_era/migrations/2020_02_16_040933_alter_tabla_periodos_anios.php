<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablaPeriodosAnios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->string('nombre',50);
            $table->smallInteger('dias_disponibles');
            $table->dropColumn('anio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->smallInteger('anio');
            $table->dropColumn('dias_disponibles');
            $table->dropColumn('nombre');
        });
    }
}
