<?php

  namespace App\Utilidades;

  use Carbon\Carbon;
  use App\Feriado;
  class FechaUtils{

    /**
     * Busca la cantidad de días existentes desde la fecha de inicio, considerando
     * que no existan feriados PERO CONSIDERANO FIN DE SEMANA
     * @param  Carbon $fechaInicio fecha desde la cual buscar
     * @param  Integer $cantidad   cantidad de días a buscar
     * @return Array arreglo asociativo con las fechas en formato Carbon
     */
    public static function getDiasHabilesIncWeekend($fechaInicio, $cantidad){

      $contador = 0;
      $fechaAux = $fechaInicio->copy();
      $fechas = array();

      while($contador < $cantidad){

         $feriado = Feriado::find($fechaAux->startOfDay());
         //si no hay un feriado en esa fecha y no es fin de semana
         //, entonces la consideramos valida
         if($feriado == null){
           $fechas[] = $fechaAux->toDateString();
           ++$contador;
         }
         $fechaAux = $fechaAux->copy();
         $fechaAux->addDay();
      }

      return $fechas;
    }



    /**
     * Busca la cantidad de días existentes desde la fecha de inicio, considerando
     * que no existan feriados
     * @param  Carbon $fechaInicio fecha desde la cual buscar
     * @param  Integer $cantidad   cantidad de días a buscar
     * @return Array arreglo asociativo con las fechas en formato Carbon
     */
    public static function getDiasHabiles($fechaInicio, $cantidad){

      $contador = 0;
      $fechaAux = $fechaInicio->copy();
      $fechas = array();

      while($contador < $cantidad){

         $feriado = Feriado::find($fechaAux->startOfDay());
         //si no hay un feriado en esa fecha y no es fin de semana
         //, entonces la consideramos valida
         if($feriado == null && !$fechaAux->isWeekend()){
           $fechas[] = $fechaAux;
           ++$contador;
         }
         $fechaAux = $fechaAux->copy();
         $fechaAux->addDay();
      }

      return $fechas;
    }



        /**
         * Busca la cantidad de días existentes desde la fecha de inicio, considerando
         * que no existan feriados
         * @param  Carbon $fechaInicio fecha desde la cual buscar
         * @param  Integer $cantidad   cantidad de días a buscar
         * @return Array arreglo asociativo con las fechas en formato string
         */
        public static function getDiasHabilesString($fechaInicio, $cantidad){

          $contador = 0;
          $fechaAux = $fechaInicio->copy();
          $fechas = array();

          while($contador < $cantidad){

             $feriado = Feriado::find($fechaAux->startOfDay());
             //si no hay un feriado en esa fecha y no es fin de semana
             //, entonces la consideramos valida
             if($feriado == null && !$fechaAux->isWeekend()){
               $fechas[] = $fechaAux->toDateString();
               ++$contador;
             }
             $fechaAux = $fechaAux->copy();
             $fechaAux->addDay();
          }

          return $fechas;
        }

    /**
     * Busca la cantidad de días habiles e inhabiles existentes
     * desde la fecha de inicio, considerando
     * que no existan feriados
     * @param  Carbon $fechaInicio fecha desde la cual buscar
     * @param  Integer $cantidad   cantidad de días a buscar
     * @return Array arreglo con las fechas en formato Carbon
     */
    public static function getDiasHabilesInhabiles($fechaInicio, $cantidad){

      $contador = 0;
      $fechaAux = $fechaInicio->copy();
      $fechas = array();
      $fechasInhabiles = array();
      while($contador < $cantidad){

         $feriado = Feriado::find($fechaAux->startOfDay());
         //si no hay un feriado en esa fecha y no es fin de semana
         //, entonces la consideramos valida
         if($feriado == null && !$fechaAux->isWeekend()){
           $fechas[] = $fechaAux;
           ++$contador;
         } else{
           //en el caso de que sea feriado o fin de semana, se agrega como inhabil
           $fechasInhabiles[] = $fechaAux;
           //PERO NO SE INCREMENTA EL CONTADOR, YA QUE BUSCO HABILES!
         }
         $fechaAux = $fechaAux->copy();
         $fechaAux->addDay();
      }

      return array("habiles"=> $fechas, "inhabiles"=> $fechasInhabiles);
    }
    /**
     * Toma una representación de texto de una fecha y la convierte
     * a una instancia de Carbon
     * @param String $txtFecha Fecha en String
     * @param String $formato formato en el cual la fecha es provisto,
     * debe ser un formato DateTime valido, por defecto yyyy-MM-dd
     * @return Carbon instancia carbon de la fecha, o null en caso de que
     * el texto sea null
     */
    public static function getFechaCarbon($txtFecha, $formato= "Y-m-d"){
      $fecha= null;
      if($txtFecha != null){
        $fecha = Carbon::createFromFormat($formato, $txtFecha);
      }
      return $fecha;
    }

    public static function getDiffDias($fechaMayor, $fechaMenor){
      return $fechaMayor->diffInDays($fechaMenor);
    }
  }
