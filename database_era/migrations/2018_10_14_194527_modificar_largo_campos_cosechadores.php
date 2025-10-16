<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarLargoCamposCosechadores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cosechadores', function (Blueprint $table) {
            $table->string("rut",20)->change();
            $table->string("nombre",100)->change();

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
            $table->string("rut")->change();
            $table->string("nombre")->change();
        });
    }
}
