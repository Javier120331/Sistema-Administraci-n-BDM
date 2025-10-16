<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSueldosBasesTable extends Migration
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
            $table->integer("valor")->unsigned();
            $table->tinyInteger("estado")
              ->comment("1 habiitado, 0 deshabilitado")
              ->default(1);
            $table->date("fecha")->nullable()->default(null);
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
        Schema::drop('sueldos_bases');
    }
}
