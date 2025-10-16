<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Representa el registro de las vacaciones de un empleado en un
 * instante determinado de tiempo.
 * @date 2019-09-15
 */
class CreateTableVacaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("empleado_id")->unsigned();
            $table->date("fecha_registro")->useCurrent();
            $table->date("fecha_inicio");
            $table->smallInteger("cantidad_dias");
            $table->date("fecha_termino");
            $table->tinyInteger("estado")->default(1);
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
        Schema::dropIfExists('vacaciones');
    }
}
