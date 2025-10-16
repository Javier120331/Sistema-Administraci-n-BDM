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