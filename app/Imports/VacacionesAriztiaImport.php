<?php
namespace App\Imports;

use App\Empleado;
use App\Periodo;
use App\Vacacion;
use App\Utilidades\EmpleadoUtils;
use App\Utilidades\VacacionesUtils;
use App\DAO\PeriodosService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use DB;
use Response;
use App\DAO\VacacionesService;
/**
 * Efectua la importacion de la información historica de ariztia
 * para las Vacaciones
 * la estructura del fichero csv es:
 * Vacaciones
 rut|fecha inicio|fecha fin| dias tomados| dias totales
 */
class VacacionesAriztiaImport implements ToCollection, WithCustomCsvSettings
{
    private $vacacionesJorge = array();
    private $trabajadoresYaProcesados = array();
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
           throw $ex;
           DB::rollBack();

      }catch(Exception $ex){
           throw $ex;
           DB::rollBack();
      }
    }

    private function procesarFila($row){
      //Convertimos rut a int para evitar el decimal
      //agregado por laravel excel

      $rut= intval($row[0]);
      
      $existia = false;
      if(array_key_exists($rut,$this->trabajadoresYaProcesados)){
        $empleado = $this->trabajadoresYaProcesados[$rut];
        $existia = true;
      } else {
        $empleado = Empleado::where('rut','LIKE',$rut.'%')->first();
        if($empleado != null){
          
          $this->trabajadoresYaProcesados[$rut] = $empleado;
         
        }
      }
      if($empleado == null){

        //Si el empleado no existe, generamos uno historico
        $empleado = EmpleadoUtils::generarEmpleadoHistorico($rut);
        $this->trabajadoresYaProcesados[$rut] = $empleado;
      }
      $flagEmpleadoBuscado= $empleado->id == 143;

      $fechaInicio = Carbon::createFromFormat('m/d/Y',$row[1]);
      $fechaTermino = Carbon::createFromFormat('m/d/Y',$row[2]);
     
      $periodo = PeriodosService::getUltimoPeriodo($empleado
        ,$fechaInicio);
//        if($flagEmpleadoBuscado){
  //        dd([$fechaInicio, $fechaTermino, $empleado, $periodo]);
    //    }
      //El periodo puede ser nulo cuando el empleado es historico
      if($periodo!= null && $periodo->id == null){
        //TODO: Este calculo es correcto?
        $periodo->dias_disponibles
          = VacacionesUtils::getDiasFeriadoProgresivo($empleado
          ,$fechaInicio) + 15;
   
        $periodo->save();
      }else if($periodo == null){ //si el periodo es nulo, significa que se
        //trata de un trabajador historico, por lo cual creamos uno de fantasia
        $periodo = new Periodo();
        $periodo->empleado_id = $empleado->id;
        $periodo->nombre = $fechaInicio->year."-".$fechaTermino->year;
        $periodo->fecha_inicio= $fechaInicio;
        $periodo->fecha_termino = $fechaTermino;
        //TODO: Vale la pena hacer esto con un historico, me imagino que si
        $periodo->dias_disponibles
          = VacacionesUtils::getDiasFeriadoProgresivo($empleado
        ,$fechaInicio) + 15;
        $periodo->dias_autorizados = 0;
        $periodo->save();
      }
      //Si no existia en el procesamiento implica que es
      //primera vez que se importa, por lo cual definimos
      //los dias originales del periodo
      $cantidadDias = $row[3];
      $cantidadTotales = $row[4];

  
      //Buscamos si ya existe un registro con ese valor
      //ya que en el fichero de ariztia vienen duplicados
      $vacacionYaExistente
        = Vacacion::where("periodo_id",'=',$periodo->id)
          ->where('fecha_inicio','=',$fechaInicio->format('Y-m-d'))
          ->where('fecha_termino','=',$fechaTermino->format('Y-m-d'))
          ->first();
      if(!$vacacionYaExistente){
        $vacacion = new Vacacion();
        $vacacion->periodo_id=$periodo->id;
        $vacacion->saldo_periodo = $periodo->dias_disponibles;
        $vacacion->dias_ya_autorizados = $periodo->dias_autorizados;
        //Reducimos las vacaciones disponibles en funcion del periodo al que pertenecen (utilizando todos los dias disponibles
        //hasta el periodo actual)
        VacacionesService::disminuirDias($empleado, $cantidadDias, $periodo);
        //Ya que es historico, lo dejamos definido como el dia de inicio
        //de las vacaciones
        $vacacion->fecha_registro = $fechaInicio;
        $vacacion->fecha_inicio = $fechaInicio;
        $vacacion->fecha_termino = $fechaTermino;
        $vacacion->cantidad_dias = $cantidadDias;
        $vacacion->dias_totales = $cantidadTotales;

        $vacacion->saldo_pendiente_periodo = $periodo->dias_disponibles;
        $periodo->save();
        $vacacion->save();
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
