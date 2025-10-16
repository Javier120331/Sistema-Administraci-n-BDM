<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTiposBonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_bonos', function (Blueprint $table) {
            $table->increments('id');
            $table->string("nombre");
            $table->tinyInteger("tipo")
                ->default(1)
                ->comment("1 imponible 0 no imponible");
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
        Schema::drop('tipos_bonos');
    }
}
