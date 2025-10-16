<?php

  namespace App\DAO;

  use Carbon\Carbon;
  use App\Periodo;
  use App\Utilidades\VacacionesUtils;
  class PeriodosService{

      
      public static function getPeriodo($empleado, $nombre){
        $periodos = self::getPeriodos($empleado);
        $periodo = $periodos->where('nombre','=',$nombre)->first();
        if($periodo->id == null){
          $periodo->save();
        }
        return $periodo;
      }

      public static function getUltimoPeriodo($empleado, $fechaLimite){
        $periodos = self::getPeriodos($empleado);
        $periodo = $periodos
          ->where('fecha_inicio','<=', $fechaLimite)
          ->where('fecha_termino', '>=', $fechaLimite)->first();
        return $periodo;
      }

      public static function getPeriodos($empleado, $fechaLimite = null){
          //1. Obtener periodos ya ingresados
          if($fechaLimite == null){
            $periodos = $empleado->periodos()->orderBy('fecha_inicio','ASC')->get();
          }else {
            
            $periodos = $empleado->periodos()
              ->where('fecha_termino', '<=', $fechaLimite)->orderBy('fecha_inicio','ASC')->get();
          }
          if($periodos->count() == 0){
              $fechaInicioCalculo = $empleado->fecha_inicio_contrato;
          } else {
            $fechaInicioCalculo = $periodos->first()->fecha_termino;
          }
          if($fechaLimite == null){
            $fechaLimite = Carbon::now();
          }
          while($fechaInicioCalculo->isBefore($fechaLimite)){
               $inicioPeriodo = $fechaInicioCalculo->copy();
               $terminoPeriodo = $fechaInicioCalculo->copy()->addYear();
               $nombrePeriodo = $inicioPeriodo->year
                  ."-".$terminoPeriodo->year;
               //solo lo agregamos si el periodo no existe en la lista
               if(!$periodos->contains('nombre',$nombrePeriodo)){
                 $periodo = new Periodo();
                 $periodo->empleado_id=$empleado->id;
                 $periodo->fecha_inicio = $inicioPeriodo;
                 $periodo->fecha_termino= $terminoPeriodo;
                 $periodo->nombre=$nombrePeriodo;

                 //Por defecto definimos los dias disponibles como 15, pero depende
                 //de las progresivas
                 $periodo->dias_disponibles = VacacionesUtils::CANTIDAD_DIAS_BASE_VACACIONES;
                 //si tiene progresivas, agregamos la base en funcion del periodo
                 //TODO:Verificar si es lo correcto
                 $vacaProgre= $periodo->empleado()->first()
                  ->vacacionesProgresivas()->first();
                 if($vacaProgre!=null){
                   $cantidadProgre = VacacionesUtils::getDiasFeriadoProgresivo($empleado
                    ,$periodo->fecha_inicio);
              
                  $periodo->dias_disponibles+=$cantidadProgre;
                 }
                 $periodo->dias_autorizados = 0;
                 $periodo->save();
                 $periodos->push($periodo);
               }
               $fechaInicioCalculo=$fechaInicioCalculo->copy()->addYear();
          }
        return $periodos;
      }

  }
