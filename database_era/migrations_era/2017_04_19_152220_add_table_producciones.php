<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableProducciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producciones', function (Blueprint $table) {
            $table->increments('id');
            $table->date("fecha")->nullable()->default(null);
            $table->integer("numero_dia")->nullable()->default(null);
            $table->integer('tipo_produccion_id')->unsigned();
            $table->integer('cosechador_id')->unsigned();
            $table->foreign("tipo_produccion_id")
              ->references("id")
              ->on("tipos_producciones")
              ->onDelete("cascade");
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
        Schema::drop('producciones');
    }
}
