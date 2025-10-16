<?php

namespace App\DAO;

use App\Vacacion;
use Carbon\Carbon;
use DB;
use App\DAO\PeriodosService;
class VacacionesService
{

  /**
   * Efectua una disminución de los dias de vacaciones disponibles del personal en base a una cantidad a tomar
   * y el periodo limite.
   * El sistema reduce los dias partiendo del primer periodo con dias disponibles hasta el ultimo que sea necesario.
   */
  public static function disminuirDias($empleado, $cantidad, $periodoLimite){
    $periodosActuales = PeriodosService::getPeriodos($empleado, $periodoLimite->fecha_termino);
    //recorremos disminuyendo los dias hasta que se cumplan
    foreach($periodosActuales as $periodoActual){
        if($periodoActual->dias_disponibles > 0){
            //15 //40
            //Si la cantidad es menor hacemos reduccion parcial de dias
            if($periodoActual->dias_disponibles < $cantidad){
                $cantidad-= $periodoActual->dias_disponibles;
                $periodoActual->dias_autorizados += $periodoActual->dias_disponibles;
                $periodoActual->dias_disponibles = 0;
  
            } else {
                //Sino quiere decir que se hace una reduccion del total en este periodo;
                $periodoActual->dias_autorizados+= $cantidad;
                $periodoActual->dias_disponibles -=$cantidad;
                $cantidad = 0;
            }
            $periodoActual->save();
            //Si se llegó a la cantidad necesaria, detenemos el ciclo
            if($cantidad == 0){
                break;
            }
        }
    }
  }


  public static function getVacaciones($filtro=null,$inicio=null,$cantidad=null, $columnaOrden=null, $tipoOrden=null){
      $sql ="SELECT v.id as id,e.rut as rut_empleado, e.nombre as nombre_empleado"
            .",v.cantidad_dias as cantidad_dias"
            .", DATE_FORMAT(v.fecha_registro , '%d/%m/%Y') as fecha_registro,DATE_FORMAT(v.fecha_inicio , '%d/%m/%Y')  as fecha_inicio, DATE_FORMAT(v.fecha_termino , '%d/%m/%Y')  as fecha_termino"
            ." FROM vacaciones v"
            ." INNER JOIN periodos p ON p.id=v.periodo_id"
            ." INNER JOIN empleados e ON e.id = p.empleado_id"
            ." WHERE 1=1";
        
       if($filtro != null){
           $sql .=" AND(  e.rut LIKE '%$filtro%'"
            ." OR e.nombre LIKE '%$filtro%'"
            ." OR DATE_FORMAT(v.fecha_registro, '%d/%m/%Y') LIKE '%$filtro%'"
            ." OR DATE_FORMAT(v.fecha_inicio, '%d/%m/%Y') LIKE '%$filtro%'"
            ." OR DATE_FORMAT(v.fecha_termino, '%d/%m/%Y') LIKE '%$filtro%')";
       }
        $sql.=" ORDER BY fecha_inicio DESC";
        if($columnaOrden!= null && $tipoOrden != null){
            $sql.=",$columnaOrden $tipoOrden";
        }
        if($inicio != null && $cantidad != null){
            $sql.=" LIMIT $inicio,$cantidad";
        }
    return DB::select($sql);
  }

  public static function getCantidadVacaciones($filtro=null,$inicio=null,$cantidad=null, $columnaOrden=null, $tipoOrden=null){
    $sql ="SELECT COUNT(*) as cantidad"
          ." FROM vacaciones v"
          ." INNER JOIN periodos p ON p.id=v.periodo_id"
          ." INNER JOIN empleados e ON e.id = p.empleado_id"
          ." WHERE 1=1";
      
     if($filtro != null){
         $sql .=" AND(  e.rut LIKE '%$filtro%'"
          ." OR e.nombre LIKE '%$filtro%'"
          ." OR DATE_FORMAT(v.fecha_registro, '%d/%m/%Y') LIKE '%$filtro%'"
          ." OR DATE_FORMAT(v.fecha_inicio, '%d/%m/%Y') LIKE '%$filtro%'"
          ." OR DATE_FORMAT(v.fecha_termino, '%d/%m/%Y') LIKE '%$filtro%')";
     }
     
      if($inicio != null && $cantidad != null){
          $sql.=" LIMIT $inicio,$cantidad";
      }
  return DB::select($sql)[0]->cantidad;
}

  /**
   * Obtiene los dias disponibles existentes hasta el periodo determinado
   */
   public static function getDiasDisponiblesHastaPeriodo($empleado, $periodo){

        $periodosActuales = PeriodosService::getPeriodos($empleado, $periodo->fecha_termino);
        $diasDisponibles = 0;
        foreach($periodosActuales as $periodoActual){
            $diasDisponibles+=$periodoActual->dias_disponibles;
        }

        return $diasDisponibles;
    }

}