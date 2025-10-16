<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFiniquitos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finiquitos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('empleado_id')->unsigned()->nullable();
            $table->string("comuna_empresa",50);
            $table->date("fecha_documento");
            $table->string("nombre_empresa",100);
            $table->string("rol_empresa",15);
            $table->string("nombre_empleador", 80);
            $table->string("rut_empleador",15);
            $table->string("nombre_empleado",80);
            $table->string("rut_empleado",15);
            $table->string("domicilio_empleado", 100);
            $table->string("domicilio_empleador", 100);
            $table->string("comuna_empleado", 50);
            $table->string("comuna_empleador", 50);
            $table->date("fecha_inicio_contrato");
            $table->date("fecha_finiquito");
            $table->string("causa_finiquito",250);
            $table->string("articulo_finiquito",60);
            $table->string("numero_articulo_finiquito",10);
            $table->integer("total_pagar");
            $table->integer("causa_finiquito_id")->unsigned();
            $table->foreign("causa_finiquito_id")
                  ->references("id")
                  ->on("causas_finiquitos")
                  ->onDelete("cascade");

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
        Schema::dropIfExists('finiquitos');
    }
}
