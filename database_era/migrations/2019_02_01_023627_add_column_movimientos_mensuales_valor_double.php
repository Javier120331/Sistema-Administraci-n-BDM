<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class AddColumnMovimientosMensualesValorDouble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientos_mensuales', function (Blueprint $table) {
            DB::statement('ALTER TABLE `movimientos_mensuales` MODIFY `valor` INTEGER NULL;');
            $table->double("valor_double",10,4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimientos_mensuales', function (Blueprint $table) {
            DB::statement('ALTER TABLE `movimientos_mensuales` MODIFY `valor` INTEGER NOT NULL;');
            $table->dropColumn("valor_double");
        });
    }
}
