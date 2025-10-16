<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSueldosBaseSinNub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('sueldos_bases', function (Blueprint $table) {
          $table->increments('id');
          $table->timestamps();
          $table->integer("estado")->default(1)
          ->comment("1 asignado, 0 removido ya no es el actual");
          $table->integer("empleado_id")->unsigned();
          $table->foreign("empleado_id")
                ->references("id")
                ->on("empleados")
                ->onDelete("cascade");
          $table->integer("valor")->unsigned();
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
        Schema::dropIfExists('sueldos_bases');
    }
}
