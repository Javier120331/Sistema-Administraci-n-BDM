<?php

namespace App\DAO;


use Carbon\Carbon;
use DB;
class FiniquitosService{

     /**
      * Devuelve los datos de sueldo requeridos para construir el finiquito del personal
      * @param  Integer $idEmpleado
      * @return DB Row
      */
     public static function getUltimos3Meses($idEmpleado){

        $sql = " SELECT sb.valor_total as 'sueldo_total', sb.fecha_asignacion as 'fecha'  "
               ." FROM sueldos_bases sb"
               ." WHERE sb.empleado_id=$idEmpleado"
          		 ." AND NOT EXISTS (SELECT ma.id FROM movimientos_asistencias ma WHERE (ma.tipo_asistencia='LI' OR ma.tipo_asistencia='VAC')"
          						." AND ma.fecha between DATE_ADD(sb.fecha_asignacion, INTERVAL -DAY(sb.fecha_asignacion) + 1 DAY)"
              ." AND LAST_DAY(sb.fecha_asignacion)"
              ." AND ma.empleado_id=$idEmpleado)"
              ." ORDER BY sb.fecha_asignacion DESC	"
            ." LIMIT 0,3";
        return DB::select($sql);
     }


}
