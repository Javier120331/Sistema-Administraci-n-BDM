<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
/**
 * Agrega campos faltantes en la tabla de finiquitos
 * incluyendo la de cargo y calculos adicionales
 */
class AddColumnsToFiniquitos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finiquitos', function (Blueprint $table) {
            $table->string("cargo_empleado",100);
            $table->integer("mes_aviso")->unsigned()->nullable();
            $table->integer("anios_servicio")->unsigned()->nullable();
            $table->integer("indemnizacion_anios_servicio")
              ->unsigned()->nullable();
            $table->integer("dias_habiles_vacaciones")
              ->unsigned()->nullable();
            $table->integer("total_dias_habiles_vacaciones")
              ->unsigned()->nullable();
            $table->integer("remuneracion_promedio")
                ->unsigned()->nullable();
            $table->integer("seguro_desempleo")->unsigned()->nullable();
            $table->integer("prestamo_empresa")->unsigned()->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finiquitos', function (Blueprint $table) {
            $table->dropColumn("mes_aviso");
            $table->dropColumn("anios_servicio");
            $table->dropColumn("indemnizacion_anios_servicio");
            $table->dropColumn("dias_habiles_vacaciones");
            $table->dropColumn("total_dias_habiles_vacaciones");
            $table->dropColumn("seguro_desempleo");
            $table->dropColumn("prestamo_empresa");
            $table->dropColumn("remuneracion_promedio");
            $table->dropColumn("cargo_empleado");
        });
    }
}
