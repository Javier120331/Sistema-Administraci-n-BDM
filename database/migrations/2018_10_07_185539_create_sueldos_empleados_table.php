<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSueldosEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sueldos_empleados', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer("estado")->default(1)
            ->comment("1 asignado, 0 removido ya no es el actual");
            $table->integer("empleado_id")->unsigned();
            $table->foreign("empleado_id")
                  ->references("id")
                  ->on("empleados")
                  ->onDelete("cascade");
            $table->integer("sueldos_base_id")->unsigned();
            $table->foreign("sueldos_base_id")
                  ->references("id")
                  ->on("sueldos_bases")
                  ->onDelete("cascade");
            $table->date("fecha_asignacion")
              ->comment("fecha en la cual asignaron el sueldo")
              ->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sueldos_empleados');
    }
}
