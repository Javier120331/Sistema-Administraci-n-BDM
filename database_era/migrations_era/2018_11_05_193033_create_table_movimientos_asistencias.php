<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMovimientosAsistencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_asistencias', function (Blueprint $table) {
            $table->increments('id');
            $table->date("fecha")->nullable()
              ->default(null)
              ->comment("fecha del evento");
            $table->integer("empleado_id")->unsigned();
            $table->foreign("empleado_id")
                  ->references("id")
                  ->on("empleados")
                  ->onDelete("cascade");
            $table->string("tipo_asistencia", 10);
            $table->time("hora_entrada")->nullable()->default(null);
            $table->time("hora_llegada")->nullable()->default(null);
            $table->integer("estado")
                  ->default(1)
                  ->comment("Estado del registro, 1 habilitado, 0 deshabilitado");
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
        Schema::dropIfExists('movimientos_asistencias');
    }
}
