<?php
namespace App\Utilidades;
use App\MovimientoAsistencia;

class MovimientosUtils{
    //TODO: Preguntar cuales son los códigos para esto ?
    public static $TIPOS_MOVIMIENTOS = [
      'PG' =>'Permiso con Goce',
      'PSG' => 'Permiso sin Goce',
      'IN' => 'Inasistencia Injustificada',
      'INJ'=> 'Inasistencia Justificada',
      'AT' => 'Atraso',
      'AC' => 'Accidente del Trabajo',
      'PS' => 'Permiso Sindical',
      'LI' => 'Licencia Médica',
    ];

    //Tipos de movimientos que generan una descarga de documento
    public static $TIPOS_MOVIMIENTOS_DESCARGA = [
      "PG",
      "PSG",
      "IN",
      "AT"
    ];

    const TIPO_LICENCIA = "LI";
    const TIPO_VACACIONES = "VAC";

    public static function ingresarMovimientosEmpleado($empleado
          , $fechas, $tipo, $horaEntrada, $horaLlegada
          , $idVacaciones = null, $idLicencia =null, $idMovimientoExport = null){

          for($i=0; $i < count($fechas); ++$i){
            $movimientoAsistencia = new MovimientoAsistencia();
            $movimientoAsistencia->empleado_id = $empleado->id;
            $movimientoAsistencia->fecha = $fechas[$i];
            $movimientoAsistencia->tipo_asistencia = $tipo;
            $movimientoAsistencia->hora_entrada = $horaEntrada;
            $movimientoAsistencia->hora_llegada = $horaLlegada;
            $movimientoAsistencia->estado = 1;
            if($idVacaciones != null){
              $movimientoAsistencia->vacaciones_id = $idVacaciones;
            } else if($idLicencia != null){
              $movimientoAsistencia->licencia_id = $idLicencia;
            }
            if($idMovimientoExport != null){
              $movimientoAsistencia->exportacion_id = $idMovimientoExport;
            }

            $movimientoAsistencia->save();
          }
      }

      public static function isFechasValidas($empleado,$fechas,$tipo){

        $valido = true;

          for($i=0; $i< count($fechas); ++$i){
            $movimientoAsistencia = MovimientoAsistencia::getByEmpleadoAndFechaAndTipo($empleado->id
              ,$fechas[$i],$tipo)->first();

            if($movimientoAsistencia != null){

              $valido = false;
              break;
            }
          }
        return $valido;
      }
}
