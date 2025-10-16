<?php

namespace App\DAO;


use Carbon\Carbon;
use DB;
class LicenciasService{

  /**
   * Elimina los movimientos que interfieran con las fechas proporcionadas
   * @param  Empleado $empleado     empleado
   * @param  Carbon $fechaInicio  inicio del rango
   * @param  Carbon $fechaTermino fin del rango
   * @return void
   */
  public static function detenerMovimientosConflictivos($empleado
    , $fechaInicio,$fechaTermino){
      $sql = "DELETE FROM movimientos_asistencias"
            ." WHERE empleado_id=".$empleado->id
            ." AND  fecha BETWEEN '".$fechaInicio->toDateString()."'"
            ." AND '".$fechaTermino->toDateString()."'";

      DB::statement($sql);
  }
  public static function detenerVacaciones($empleado
    , $fechaInicio, $fechaTermino,$idVacaciones){

      $sql = "DELETE FROM movimientos_asistencias"
            ." WHERE empleado_id=".$empleado->id
            ." AND vacaciones_id =".$idVacaciones
						." AND (fecha BETWEEN '".$fechaInicio->toDateString()."'"
            ." AND '".$fechaTermino->toDateString()."'"
            ." OR fecha > '".$fechaTermino->toDateString()."')";

      DB::statement($sql);

  }

}
