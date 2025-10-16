<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargosEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargos_empleados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("empleado_id")->unsigned();
            $table->integer("cargo_id")->unsigned();
            $table->tinyInteger("estado")
              ->comment("1 para habilitado 0 para deshabilitado")
              ->default(1);
            $table->foreign("empleado_id")
                ->references("id")
                ->on("empleados")
                ->onDelete("cascade");
            $table->foreign("cargo_id")
                ->references("id")
                ->on("cargos")
                ->onDelete("cascade");
            $table->date("fecha_inicio_cargo")->nullable()->default(null);
            $table->date("fecha_termino_cargo")->nullable()->default(null);
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
        Schema::drop('cargos_empleados');
    }
}
