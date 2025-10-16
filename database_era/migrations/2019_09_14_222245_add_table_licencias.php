<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableLicencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licencias', function (Blueprint $table) {
            $table->increments('id');
            $table->engine = 'InnoDB';
            $table->date("fecha_inicio")->useCurrent();
            $table->smallInteger("cant_dias")->unsigned();
            $table->date("fecha_termino")->nullable();
            $table->string("folio_licencia")->nullable();
            $table->string("antecedentes", 500);
            $table->tinyInteger("estado")->default(1);
            $table->integer("empleado_id")->unsigned();
            $table->foreign("empleado_id")
                  ->references("id")
                  ->on("empleados")
                  ->onDelete("cascade");
            $table->integer("causa_licencia_id")->unsigned();
            $table->foreign("causa_licencia_id")
                  ->references("id")
                  ->on("causas_licencias")
                  ->onDelete("cascade");
            //asegurarse que no exista más de una licencia para
            //el empleado en la fecha
            $table->unique(["empleado_id",'fecha_inicio'],"unica_licencia_emp");
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
        Schema::dropIfExists('licencias');
    }
}
