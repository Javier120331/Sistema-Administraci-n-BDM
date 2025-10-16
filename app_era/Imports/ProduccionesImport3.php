<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Imports\ImportacionesProduccionBase;
class ProduccionesImport3 extends ImportacionesProduccionBase implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
      foreach($collection as $fila){
        $this->procesarRegistroArchivoProduccion($fila);
      }
    }
}
