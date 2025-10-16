<?php
namespace App\Utilidades;
use Carbon\Carbon;
use App\DAO\PeriodosService;
class VacacionesUtils{

  const CANTIDAD_DIAS_BASE_VACACIONES=15;

  public static function getDiasFeriadoProgresivo($empleado, $fechaVacacion){
    if($empleado->vacacionesProgresivas()->first() != null){
      return self::getCantidadDiasAdicionales($fechaVacacion
        , $empleado->fecha_inicio_contrato
        , $empleado->vacacionesProgresivas()->first()->cantidad_anios);
    }else{
      return 0;
    }
  }

  /**
   * Efectua el cálculo de días adicionales a agregar a un trabajador
   * que ingresa vacaciones progresivas
   * @param  Carbon $fechaInicio Fecha en la cual se debe inicializar
   * las vacaciones progresivas
   * @param  Carbon $fechaContrato Fecha en la cual comenzó a trabajar
   * en Bosques del Mauco
   * @param  Integer $anios  cantidad de años trabajados previamente
   * en la empresa anterior
   * @return Integer cantidad de días a agregar de vacaciones
   */
  public static function getCantidadDiasAdicionales($fechaInicio
    ,$fechaContrato, $anios){

    //El -1 es porque se consideran cumplidos
    $cantidadReal = $fechaInicio->startOfDay()->diffInYears($fechaContrato);
    
    $cantidadReal+=$anios;
    //Inicialmente partimos con un día adicional si tiene al menos 10 años en la empresa
    //Ya que al menos tenemos 13 años de trabajo
    //Restamos a la cantidad 13 años para efectuar mods con el fin de
    //obtener cuantos días adicionales se deben agregar
    
    $diasAcumulados = 0;
    if($cantidadReal >= 13){
      $cantidadReal -=13;

      $diasAcumulados = 1;

      while($cantidadReal >= 3){
          //Restamos 3  años, ya que con 3 se puede agregar un
          //día adicional
          $cantidadReal-=3;
          //si dio negativo, entonces no tiene la cantidad suficiente
          if($cantidadReal >= 0){
            ++$diasAcumulados;
          }
        }
    }
    return $diasAcumulados;
  }

  public static function getValorVacaciones($diasHabiles,$remuneracionPromedio){
    return round($diasHabiles * ($remuneracionPromedio/30));
  }

  public static function getValorVacacionesByEmp($empleado
    ,$remuneracionPromedio, $fechaFiniquito){
      return self
        ::getValorVacaciones(self
        ::getDiasDisponiblesVacaciones($empleado,$fechaFiniquito), $remuneracionPromedio);
  }
  public static function getDiasDisponiblesVacaciones($empleado
    , $fechaFiniquito){
      $ultimoPeriodo = PeriodosService::getUltimoPeriodo($empleado,$fechaFiniquito);


      return $ultimoPeriodo->dias_disponibles;
  }

  private static function getDiffDias($fechaInicial){

     $fechaActual = Carbon::now();
     $diffInterval = $fechaActual->diffAsCarbonInterval($fechaInicial);


     $diasRecomendados = $diffInterval->years*12*1.25;
     $diasRecomendados += $diffInterval->months * 1.25;
     $diasRecomendados += $diffInterval->dayz * 0.04167;

     return round($diasRecomendados,2);
  }

}
