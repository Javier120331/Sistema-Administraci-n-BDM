<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("valor")->unsigned();
            $table->date("fecha")->nullable()
              ->default(null)
              ->comment("fecha en la cual se entrego el bono");
            $table->integer("empleado_id")->unsigned();
            $table->foreign("empleado_id")
                  ->references("id")
                  ->on("empleados")
                  ->onDelete("cascade");
            $table->integer("tipo_bono_id")->unsigned();
            $table->foreign("tipo_bono_id")
              ->references("id")
              ->on("tipos_bonos")
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
        Schema::drop('bonos');
    }
}
