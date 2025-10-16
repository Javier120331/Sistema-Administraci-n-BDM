<?php

namespace App\Imports;

use App\Empleado;
use App\Periodo;
use App\Comuna;
use App\Licencia;
use App\Utilidades\EmpleadoUtils;
use App\CausaLicencia;
use App\DAO\PeriodosService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use DB;
use App\Utilidades\MovimientosUtils;
use App\Configuracion;
use App\MovimientoAsistencia;
use App\MovimientoAsistenciaExport;
use PDF;
use App\Utilidades\FechaUtils;
/**
 * Permite procesar tanto el archivo permisos.csv como sindicalesOtros.csv
 */
class PermisosAriztiaImport implements ToCollection, WithCustomCsvSettings
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
        dd($ex);
          DB::rollBack();

        }catch(Exception $ex){
          dd($ex);
          DB::rollBack();
        }
    }

    private function procesarFila($row){
      $rut= intval($row[0]);
      $empleado = Empleado::where('rut','LIKE',$rut.'%')->first();
      $fechaInicio = Carbon::createFromFormat("m/d/Y", $row[1]);
      $fechaTermino = Carbon::createFromFormat("m/d/Y",$row[2]);
      $cantidadDias = $row[3];
      $tipoMov = $this->getCodigoMovimiento($row[5]);
      if($empleado == null){
        //Si el trabajador no existia en el proceso de importación, lo
        //almacenamos como un trabajador historico
        $empleado = EmpleadoUtils::generarEmpleadoHistorico($rut);
      }
      $this->almacenarMovimientos($empleado,$fechaInicio
       , $cantidadDias, $tipoMov);
    }

    private function almacenarMovimientos($empleado
      , $fechaInicio, $cantidad, $tipo, $horaEntrada = null, $horaLlegada = null){
      $resultado = new \stdClass();
      $resultado->estado = true;
      $resultado->mensaje = null;
      $fechas = FechaUtils::getDiasHabiles($fechaInicio, $cantidad);
      $valido = MovimientosUtils::isFechasValidas($empleado
        ,$fechas, $tipo);

      if(!$valido){
        $resultado->estado= false;
        $resultado->mensaje = "Existen movimientos en el rango de fechas del mismo tipo";

      } else{
        if(in_array($tipo,MovimientosUtils::$TIPOS_MOVIMIENTOS_DESCARGA)){
          $descargable = 1;
        } else {
          $descargable = 0;
        }
        $resultado->idDocumento = $this->generarDocumento($empleado, $fechas
          , $tipo, $cantidad, $horaEntrada, $horaLlegada, $descargable);
        $idMovimientoExport = $resultado->idDocumento;
        $resultado->descargable = $descargable;
        MovimientosUtils::ingresarMovimientosEmpleado($empleado
          , $fechas, $tipo, $horaEntrada, $horaLlegada,null,null
          , $idMovimientoExport);
        $resultado->estado = true;
        $resultado->mensaje = "Ingreso de registros efectuado";

      }
      return $resultado;
    }

    private function generarDocumento($empleado
        , $fechas, $tipo, $cantidad, $horaEntrada = null, $horaLlegada = null
        , $descargable = 0){
      $fecha = Carbon::now();
      $fechaInicio = $fechas[0];
      $fechaTermino = $fechas[count($fechas)-1];
      $movimientoAsistenciaExport = new MovimientoAsistenciaExport();
      $movimientoAsistenciaExport->fecha_generacion = $fecha;
      $movimientoAsistenciaExport->titulo_documento = "Acuerdo de "
          .MovimientosUtils::$TIPOS_MOVIMIENTOS[$tipo];
      $movimientoAsistenciaExport->rut_empleado= $empleado->rut;
      $movimientoAsistenciaExport->nombre_empleado = $empleado->nombre;
      $movimientoAsistenciaExport->fecha_inicio_movimientos = $fechaInicio;
      $movimientoAsistenciaExport->cantidad_dias = $cantidad;
      if($fechaInicio->notEqualTo($fechaTermino)){
        $movimientoAsistenciaExport->fecha_termino_movimientos = $fechaTermino;
      }
      $movimientoAsistenciaExport->fecha_documento = $fecha;
      $movimientoAsistenciaExport->area = !is_null($empleado->area_id)?  $empleado->area()->first()->nombre:null;
      $movimientoAsistenciaExport->tipo_movimiento = $tipo;
      $movimientoAsistenciaExport
        ->nombre_encargado = Configuracion::getByNombre('nombre_empleador')
        ->first()->valor;

      //Para atrasos
      $movimientoAsistenciaExport->hora_entrada = $horaEntrada;
      $movimientoAsistenciaExport->hora_llegada = $horaLlegada;
      $movimientoAsistenciaExport->descargable = $descargable;
      $movimientoAsistenciaExport->save();
      return $movimientoAsistenciaExport->id;
    }
    private function getCodigoMovimiento($tipoMov){
       $codigoMov = "";
       switch($tipoMov){
         case "PERMISO CON SUELDO": $codigoMov = 'PG';
         break;
         case "PERMISO SIN SUELDO": $codigoMov = 'PSG';
         break;
         case "ATRASOS": $codigoMov = 'AT';
         break;
         case "FALLA": $codigoMov = 'IN';
         break;
         case "PERMISO SINDICAL": $codigoMov = 'PS';
         break;
       }
       return $codigoMov;
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
