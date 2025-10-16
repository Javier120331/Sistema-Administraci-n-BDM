<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAnteceTipoLicencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licencias', function (Blueprint $table) {
            $table->tinyInteger("tipo")
              ->comment("1 Dia completo 2 medio dia")->default(1);
            $table->dropColumn("antecedentes");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licencias', function (Blueprint $table) {
            $table->dropColumn("tipo");
            $table->string("antecedentes", 500);
        });
    }
}
