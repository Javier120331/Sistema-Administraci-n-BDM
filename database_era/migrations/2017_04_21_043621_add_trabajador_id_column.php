<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrabajadorIdColumn extends Migration
{
    /**
     * Agrega la columna trabajador_id, la cual estará relacionada
     * con la tabla trabajadors de Veritas
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cosechadores', function (Blueprint $table) {
            $table->integer("trabajador_id")->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cosechadores', function (Blueprint $table) {
            $table->dropColumn("trabajador_id");
        });
    }
}
