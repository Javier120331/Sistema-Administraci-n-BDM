<?php

namespace App\Imports;


use App\Empleado;
use App\Periodo;
use App\VacacionProgresiva;
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
 * para las Vacaciones progresivas
 * la estructura del fichero csv es:
 * Vacaciones
 rut|periodo
 */
class ProgresivasAriztiaImport implements ToCollection, WithCustomCsvSettings
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
        $anio = intval($row[1]);
        $fechaInicio = Carbon::createFromFormat("d/m/Y", "01/01/$anio");
        $fechaTermino = Carbon::createFromFormat("d/m/Y","31/12/$anio");
        $progresiva = $empleado->vacacionesProgresivas()->getByFechas($fechaInicio
          ,$fechaTermino)->first();
        if($progresiva == null){ //Si no ha sido registrada para el empleado
            $progresiva = new VacacionProgresiva();
            $progresiva->empleado_id = $empleado->id;
            //TODO: Se definen siempre 10 años?preguntar
            $progresiva->cantidad_anios = 10;
            $progresiva->fecha = $fechaInicio;
            $progresiva->save();
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
