<?php

namespace App\Imports;


use App\Empleado;
use App\Periodo;
use App\Licencia;
use App\CausaLicencia;
use App\Utilidades\EmpleadoUtils;
use App\DAO\PeriodosService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use DB;
/**
 * Efectua la importacion de la información historica de ariztia
 * para las licencias medicas
 * la estructura del fichero csv es:
 * rut| fecha_inicio | fecha_termino | cantidad_dias| causa */
class LicenciasAriztiaImport implements ToCollection, WithCustomCsvSettings
{
  /**
  * @param Collection $collection
  */
  public function collection(Collection $rows)
  {
    DB::beginTransaction();
    try{
      foreach ($rows as $row)
      {
        $this->procesarFila($row);
      }
      DB::commit();
    }catch(\Illuminate\Database\QueryException $ex){
         DB::rollBack();

    }catch(Exception $ex){
         DB::rollBack();
    }
  }
  private function procesarFila($row){
    $rut= intval($row[0]);
    $empleado = Empleado::where('rut','LIKE',$rut.'%')->first();
    if($empleado == null){
      //Si el empleado no existe, generamos uno historico
      $empleado = EmpleadoUtils::generarEmpleadoHistorico($rut);
    }
    $fechaInicio = Carbon::createFromFormat("m/d/Y", $row[1]);
    $fechaTermino = Carbon::createFromFormat("m/d/Y",$row[2]);
    $licencia = $empleado->licencias()
      ->getByFechas($fechaInicio,$fechaTermino)->first();
    if($licencia == null){
      $cantidadDias = $row[3];
      $nombreCausa = $row[4];
      $causaLicencia = CausaLicencia::getByNombre($nombreCausa)->first();
      if($causaLicencia == null){
        $causaLicencia = new CausaLicencia();
        $causaLicencia->nombre = $nombreCausa;
        $causaLicencia->estado = 1;
        $causaLicencia->save();
      }
      $licencia = new Licencia();
      $licencia->empleado_id = $empleado->id;
      $licencia->causa_licencia_id = $causaLicencia->id;
      $licencia->estado = 1;
      $licencia->fecha_inicio = $fechaInicio;
      $licencia->fecha_termino = $fechaTermino;
      $licencia->cant_dias = $cantidadDias;
      $licencia->save();
    }

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
      'input_encoding' => 'UTF-8',
      'delimiter'=>'|'
    ];
  }
}
