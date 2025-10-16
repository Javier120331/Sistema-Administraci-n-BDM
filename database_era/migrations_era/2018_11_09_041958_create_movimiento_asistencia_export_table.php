<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientoAsistenciaExportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_asistencias_exportaciones'
          , function (Blueprint $table) {
            $table->increments('id');
            $table->date("fecha_documento");
            $table->string("nombre_encargado", 50);
            $table->string("nombre_empleado",50);
            $table->string("rut_empleado",20);
            $table->date("fecha_inicio_movimientos");
            $table->date("fecha_termino_movimientos")
              ->nullable()->default(null);
            $table->integer("cantidad_dias")->unsigned();
            $table->string("titulo_documento", 40);
            $table->date("fecha_generacion");
            $table->string("area",40);
            $table->string("tipo_movimiento",30);
            $table->time("hora_entrada")->nullable()->default(null);
            $table->time("hora_llegada")->nullable()->default(null);
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
        Schema::dropIfExists('movimientos_asistencias_exportaciones');
    }
}
