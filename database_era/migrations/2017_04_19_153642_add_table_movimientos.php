<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableMovimientos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->float("kilos", 8, 3);
            $table->integer("produccion_id")->unsigned();
            $table->integer("calidad_id")->unsigned();
            $table->foreign("produccion_id")
              ->references("id")
              ->on("producciones")
              ->onDelete("cascade");
            $table->foreign("calidad_id")
              ->references("id")
              ->on("calidades")
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
        Schema::drop('movimientos');
    }
}
