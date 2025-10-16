<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableVeritasRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::statement('ALTER TABLE `calidades` MODIFY `id_calidad` INTEGER UNSIGNED NULL;');
      DB::statement('ALTER TABLE `cosechadores` MODIFY `trabajador_id` INTEGER UNSIGNED NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::statement('ALTER TABLE `calidades` MODIFY `id_calidad` INTEGER UNSIGNED NOT NULL;');
      DB::statement('ALTER TABLE `cosechadores` MODIFY `trabajador_id` INTEGER UNSIGNED NOT NULL;');
    }
}
