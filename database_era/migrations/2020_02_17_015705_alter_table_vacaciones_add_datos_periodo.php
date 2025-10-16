<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableVacacionesAddDatosPeriodo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vacaciones', function (Blueprint $table) {
            $table->smallInteger('saldo_periodo');
            $table->smallInteger('saldo_pendiente_periodo');
            $table->smallInteger('dias_ya_autorizados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vacaciones', function (Blueprint $table) {
          $table->dropColumn('saldo_periodo');
          $table->dropColumn('saldo_pendiente_periodo');
          $table->dropColumn('dias_ya_autorizados');
        });
    }
}
