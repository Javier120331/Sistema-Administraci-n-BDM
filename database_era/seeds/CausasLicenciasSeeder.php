<?php

use Illuminate\Database\Seeder;
class CausasLicenciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::insert("INSERT INTO causas_licencias(`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES (1, 'ENFERMEDAD O ACCIDENTE COMUN', 1, '2019-10-21 00:03:23', '2019-10-21 00:03:26')");
      DB::insert("INSERT INTO causas_licencias(`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES (2, 'PRORROGA MEDICINA PREVENTIVA', 1, '2019-10-21 00:03:23', '2019-10-21 00:03:26')");
      DB::insert("INSERT INTO causas_licencias(`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES (3, 'LICENCIA MATERNA PRE Y POST NATAL', 1, '2019-10-21 00:03:23', '2019-10-21 00:03:26')");
      DB::insert("INSERT INTO causas_licencias(`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES (4, 'ENFERMEDAD GRAVE NIÑO MENOR DE 1 AÑO', 1, '2019-10-21 00:03:23', '2019-10-21 00:03:26')");
      DB::insert("INSERT INTO causas_licencias(`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES (5, 'ACCIDENTE DEL TRABAJO O DEL TRAYECTO', 1, '2019-10-21 00:03:23', '2019-10-21 00:03:26')");
      DB::insert("INSERT INTO causas_licencias(`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES (6, 'ENFERMEDAD PROFESIONAL', 1, '2019-10-21 00:03:23', '2019-10-21 00:03:26')");
      DB::insert("INSERT INTO causas_licencias(`id`, `nombre`, `estado`, `created_at`, `updated_at`) VALUES (7, 'PATOLOGIA DEL EMBARAZO', 1, '2019-10-21 00:03:23', '2019-10-21 00:03:26')");
    }
}
