<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSueldosBasesTable extends Migration
{
    /**
     * Eliminamos la tabla, ya que es innecesario generarla mediante un NUB
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop("sueldos_bases");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::create('sueldos_bases', function (Blueprint $table) {
          $table->increments('id');
          $table->integer("valor")->unsigned();
          $table->tinyInteger("estado")
            ->comment("1 habiitado, 0 deshabilitado")
            ->default(1);
          $table->date("fecha")->nullable()->default(null);
          $table->timestamps();
      });
    }
}
