<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTablaTotalesMensuales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_mensuales', function (Blueprint $table) {
            $table->increments('id');
            $table->date("fecha")->nullable()->default(null);
            $table->string('tipo_no_produccion');
            $table->integer('cosechador_id')->unsigned();
            $table->integer("valor")->unsigned();
            $table->foreign("cosechador_id")
              ->references("id")
              ->on("cosechadores")
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
        Schema::drop('movimientos_mensuales');
    }
}
