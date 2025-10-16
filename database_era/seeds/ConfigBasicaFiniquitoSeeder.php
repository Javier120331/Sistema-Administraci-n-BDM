<?php

use Illuminate\Database\Seeder;

class ConfigBasicaFiniquitoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configuraciones')->insert([
          'nombre'=>'comuna_empresa',
          'valor'=>'Quintero',
          'created_at'=>Carbon\Carbon::now(),
          'updated_at'=>Carbon\Carbon::now(),
        ]);
        DB::table('configuraciones')->insert([
          'nombre'=>'nombre_empresa',
          'valor'=>'INVERS. BOSQUES DEL MAUCO S.A.',
          'created_at'=>Carbon\Carbon::now(),
          'updated_at'=>Carbon\Carbon::now(),
        ]);
        DB::table('configuraciones')->insert([
          'nombre'=>'nombre_empleador',
          'valor'=>'Aurora Muñoz Molina',
          'created_at'=>Carbon\Carbon::now(),
          'updated_at'=>Carbon\Carbon::now(),
        ]);
        DB::table('configuraciones')->insert([
          'nombre'=>'rol_empresa',
          'valor'=>'96970470-6',
          'created_at'=>Carbon\Carbon::now(),
          'updated_at'=>Carbon\Carbon::now(),
        ]);
        DB::table('configuraciones')->insert([
          'nombre'=>'rut_empleador',
          'valor'=>'9021883-2',
          'created_at'=>Carbon\Carbon::now(),
          'updated_at'=>Carbon\Carbon::now(),
        ]);
        DB::table('configuraciones')->insert([
          'nombre'=>'domicilio_empleador',
          'valor'=>'Parcela 40 Ex-Fundo Las Gaviotas',
          'created_at'=>Carbon\Carbon::now(),
          'updated_at'=>Carbon\Carbon::now(),
        ]);
    }
}
