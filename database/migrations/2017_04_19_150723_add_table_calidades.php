<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableCalidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calidades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("id_calidad")->unsigned()
              ->comment("Id de calidads en Veritas");
            $table->integer("valor")->unsigned();
            $table->float("factor",8, 2);
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
        Schema::drop('calidades');
    }
}
