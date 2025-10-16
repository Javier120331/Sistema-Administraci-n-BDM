<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTiposProducciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipos_producciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string("nombre");
            $table->string("abreviacion");
            $table->tinyInteger("tipo_pago")
                  ->nullable()->default(null);
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
        Schema::drop('tipos_producciones');
    }
}
