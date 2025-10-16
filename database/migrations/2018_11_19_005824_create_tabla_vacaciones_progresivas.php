<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablaVacacionesProgresivas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacaciones_progresivas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("empleado_id")->unsigned();
            $table->date("fecha")
              ->comment("Fecha en la cual comenzó a trabajar en BMauco");
            $table->integer("cantidad_anios")
              ->unsigned()
              ->comment("Corresponde a los años trabajados en otra empresa");
            $table->integer("cantidad_dias_adicionales")
              ->unsigned()
              ->comment("Corresponde a los dias agregados por periodo producto de los años trabajados");
            $table->foreign("empleado_id")
                  ->references("id")
                  ->on("empleados")
                  ->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacaciones_progresivas');
    }
}
