<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Imports\ImportacionesBase;
class VerificarSeparadorImport extends ImportacionesBase implements ToCollection
{
    public static $encontrado = false;
    public static $campoParaValidar;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
      $fila = $collection->first();
      if(array_key_exists(self::$campoParaValidar
        ,$fila->toArray())){
          self::$encontrado = true;
      }
    }
}
