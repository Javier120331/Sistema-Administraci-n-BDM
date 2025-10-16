<?php

namespace App\Imports;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportacionesBase  implements WithCustomCsvSettings
  ,WithHeadingRow
{

  public static $delimitador;

  //WORKAROUND: en vista de que laravel excel no sabe interpretar correctamente
  //WORKAROUND: los tildes, debo hacer un arreglo para verificar todas
  //WORKAROUND: las posibles llaves que puede tener el excel
  //WORKAROUND: La solucion correcta es que quiten columnas con tilde
  public static $codigosEmpleadosLlaves = ["ca3digo_empleado"
    , "codigo_empleado", "cidigo_empleado"];

  /**
  * En base a una fila de fichero de produccion, devuelve el codigo de empleado
  * asociado.
  * @param  Collection $fila fila de un fichero de produccion
  * @return String  codigo del empleado
  */
   protected function getCodigoEmpleado($fila){
     foreach(self::$codigosEmpleadosLlaves as $llave){
       $codigoEmpleado = $fila->get($llave);
       //WORKAROUND: si es que devuelve null, implica de que el tilde fue mal interpretado
       //por lo cual la columna se llama de la otra forma
       if($codigoEmpleado != null){
         return $codigoEmpleado;
       }
     }
     return null;
   }

  public function batchSize(): int
  {
    return 1000;
  }

  public function chunkSize(): int
  {
    return 1000;
  }

  public function getCsvSettings(): array
  {
    return [
      'input_encoding' => 'ISO-8859-1',
      'delimiter'=>self::$delimitador
    ];
  }

}
