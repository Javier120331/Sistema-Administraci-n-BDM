<?php

namespace App\Imports;

use App\Imports\ImportacionesBase;
use App\Utilidades\RutUtils;
use App\Trabajador;
use App\Cosechador;
use App\TipoProduccion;
use Carbon\Carbon;
use App\Produccion;
use App\CalidadVeritas;
use App\Calidad;
use App\Configuracion;
use App\Movimiento;
use App\Utilidades\ImportacionProduccionUtils;
class ImportacionesProduccionBase extends ImportacionesBase{

  public static $fecha;

  protected function getCalidadYDiaByLlave($llave){

    $texto = str_replace("proddia_", "", $llave);
    $arregloTexto = explode("_", $texto);
    $dia = $arregloTexto[0];

    if($dia >=21){
      $fechaMovimiento = self::$fecha->copy()->subMonth();
    } else {
      $fechaMovimiento = self::$fecha->copy();
    }
    $fechaMovimiento->day=$dia;

    $nombreCalidad = "";

    //nos saltamos el primero, que equivale al día,
    //el resto es parte del nombre de la calidad
    for($i=1; $i < count($arregloTexto); ++$i){
      $nombreCalidad .=$arregloTexto[$i]." ";
    }
    $nombreCalidad = trim($nombreCalidad);
    $calidadDia = new \stdClass();
    $calidadDia->dia = $fechaMovimiento;
    $calidadVeritas =  CalidadVeritas::getLikeNombre($nombreCalidad)
    ->first();
    $calidadDia->calidad = Calidad::getByCalidadVeritasId($calidadVeritas->id)
    ->first();

    return $calidadDia;
  }

  protected function procesarRegistroArchivoProduccion($fila){
    $codigoEmpleado = $this->getCodigoEmpleado($fila);
    $rut = RutUtils::sanitizar($codigoEmpleado);
    $cosechador = Cosechador::getByRut($rut)->first();
    $fila->each(function($valor, $llave) use($cosechador, $fila){

      //descartamos la llave que no nos interesa
      if(!in_array($llave, self::$codigosEmpleadosLlaves)){

        //solo si es distinto al valor del día de vacaciones o dias libres
        //procesamos
        if(!in_array($llave, ImportacionProduccionUtils::COLUMNAS_NO_PRODUCCION)){
          $calidadDia = $this->getCalidadYDiaByLlave($llave);
          $produccion = Produccion::getByFechaAndIdCosechador($calidadDia
          ->dia->toDateString(), $cosechador->id)->first();
          
          //Si es que la producción no existe, implica
          //de que es un día no laboral(ZZ) por lo cual
          //no se debe considerar.
          if($produccion != null){
            $movimiento = new Movimiento();
            $movimiento->kilos = $valor;
            $movimiento->produccion_id = $produccion->id;
            $movimiento->calidad_id = $calidadDia->calidad->id;
            $movimiento->save();
          }
        }
      }
    });
  }

}
