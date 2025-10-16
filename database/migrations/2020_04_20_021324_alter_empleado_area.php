<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmpleadoArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empleados', function (Blueprint $table) {
          DB::statement("ALTER TABLE empleados DROP FOREIGN KEY empleados_area_id_foreign, MODIFY area_id INT UNSIGNED");
          DB::statement('ALTER TABLE empleados ADD CONSTRAINT empleados_area_id_foreign FOREIGN KEY (area_id) REFERENCES areas (id)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empleados', function (Blueprint $table) {
          DB::statement("ALTER TABLE empleados DROP FOREIGN KEY empleados_area_id_foreign, MODIFY area_id INT UNSIGNED NOT NULL");
          DB::statement('ALTER TABLE empleados ADD CONSTRAINT empleados_area_id_foreign FOREIGN KEY (area_id) REFERENCES areas (id)');


        });
    }
}
