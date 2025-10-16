<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSueldoTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sueldos_bases', function (Blueprint $table) {
            $table->integer("valor_total")
              ->unsigned()
              ->comment("Corresponde al valor con todos los bonos y haberes sumados");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sueldos_bases', function (Blueprint $table) {
            $table->dropColumn("valor_total");
        });
    }
}
