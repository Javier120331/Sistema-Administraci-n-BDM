<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTablaPeriodos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('anio')->unsigned();
            $table->integer('empleado_id')->unsigned();
            $table->smallInteger('dias_autorizados')->default(0);
            $table->date("fecha_inicio");
            $table->date("fecha_termino");
            $table->timestamps();
            $table->foreign('empleado_id')
              ->references('id')
              ->on('empleados')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('periodos');
    }
}
