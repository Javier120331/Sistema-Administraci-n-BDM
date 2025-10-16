<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->increments('id');
            $table->string("rut")->unique();
            $table->string("nombre");
            $table->string("direccion")->default(null);
            $table->integer("comuna_id")->unsigned();
            $table->date("fecha_inicio_contrato")->nullable()->default(null);
            $table->date("fecha_termino_contrato")->nullable()->default(null);
            $table->foreign("comuna_id")
                ->references("id")
                ->on("comunas")
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
        Schema::drop('empleados');
    }
}
