<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnasFiniquitos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('finiquitos', function (Blueprint $table) {
                $table->integer('dias_inhabiles');
                $table->integer("total_dias_inhabiles");
                $table->integer('descuento_sobregiro');
                $table->integer('descuento_convenios');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
              Schema::table('finiquitos', function (Blueprint $table) {
                $table->dropColumn('dias_inhabiles');
                $table->dropColumn('total_dias_inhabiles');
                $table->dropColumn('descuento_sobregiro');
                $table->dropColumn('descuento_convenios');
              });
    }
}
