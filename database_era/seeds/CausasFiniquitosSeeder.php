<?php

use Illuminate\Database\Seeder;
class CausasFiniquitosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("159","1", "Mutuo acuerdo de las partes",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("159","2", "Renuncia del trabajador",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("159","3", "Muerte del trabajador",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("159","4", "Vencimiento del plazo convenido en el contrato",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("159","5", "Conclusión del trabajo o servicio que dio origen al contrato",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("159","6", "Caso fortuito o fuerza mayor",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160","1 a)","Falta de probidad del trabajador en el desempeño de sus funciones",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160","1 b)","Conductas de acoso sexual",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160","1 c)","Vías de hecho ejercidas por el trabajador en contra del empleador o de cualquier trabajador que se desempeñe en la misma empresa",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160","1 d)","Injurias proferidas por el trabajador al empleador",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160","1 e)","Conducta inmoral del trabajador que afecte a la empresa donde se desempeña",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160","1 f)","Conductas de acoso laboral",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160", "2","Negociaciones que ejecute el trabajador dentro del giro del negocio y que hubieren sido prohibidas por escrito en el respectivo contrato por el empleador",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160", "3","No concurrencia del trabajador a sus labores",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160", "4","Abandono del trabajo por parte del trabajador",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160", "5","Actos, omisiones o imprudencias temerarias",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160", "6","El perjuicio material causado intencionalmente",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("160", "7","Incumplimiento grave de las obligaciones que impone el contrato",NOW(),NOW())');
      DB::insert('INSERT INTO causas_finiquitos(articulo,numero,descripcion,created_at,updated_at) VALUES("161", "Inciso 1","Necesidades del Funcionamiento de la Empresa",NOW(),NOW())');

    }
}
