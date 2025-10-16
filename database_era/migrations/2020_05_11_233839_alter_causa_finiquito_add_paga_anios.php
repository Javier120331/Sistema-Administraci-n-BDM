<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCausaFiniquitoAddPagaAnios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('causas_finiquitos', function (Blueprint $table) {
            $table->tinyInteger('paga_anios')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('causas_finiquitos', function (Blueprint $table) {
            $table->dropColumn('paga_anios');
        });
    }
}
