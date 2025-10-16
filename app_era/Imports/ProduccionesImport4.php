<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Utilidades\RutUtils;
use App\Trabajador;
use App\Cosechador;
use Carbon\Carbon;
use App\Produccion;
use App\MovimientoMensual;
use App\Movimiento;
use App\Imports\ImportacionesProduccionBase;
class ProduccionesImport4 extends ImportacionesProduccionBase implements ToCollection
{
    public static $anio;
    public static $mes;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
      foreach($collection as $fila){
        $this->procesarRegistroArchivoProduccion($fila);
        $this->procesarValoresDiasLibres($fila);
      }
    }


    /**
     * Procesa los valores  de dias libres, utilizados para el cálculo
     * @param  CellCollection $fila una fila del CSV 4
     * @return void
     */
    private function procesarValoresDiasLibres($fila){
      $codigoEmpleado = $this->getCodigoEmpleado($fila);
      $rut = RutUtils::sanitizar($codigoEmpleado);
      $cosechador = Cosechador::getByRut($rut)->first();
      $fecha = Carbon::createFromDate(self::$anio, self::$mes, 1);

      $valorVacaciones =   $fila["valor_dias_de_vacaciones"];
      $valorDiasLibres =   $fila["total_valor_dias_libres"];

      //WORKAROUND: Con el fin de asegurar la seleccion correcta de la columna
      //WORKAROUND: se evalúa ambos casos, uno cuando el separador del excel
      //WORKAROUND: es , y otro cuando es ;
      //WORKAROUND: En el caso de la , la columna es procesada como si tuviera
      //WORKAROUND: un . , en el caso del ; va sin .
      $valorDiasPermisos =  $fila->get("valor_dias_perm._cgs");
      if($valorDiasPermisos == null){
        $valorDiasPermisos = $fila->get("valor_dias_perm_cgs");
      }
      $bonoProdCalculado =     $fila->get("bono_prod._calculado");
      if($bonoProdCalculado  == null){
        $bonoProdCalculado = $fila->get("bono_prod_calculado");
      }
      $baseValorParaDiaLibre = $fila["base_valor_para_dia_libre"];
      $totalDiasNoProd =       $fila["total_dias_no_produccion"];
      $totalValorCosechaPago = $fila["total_valor_cosecha_pago"];

      $valorDiferenciaCosechaDia = $fila["valor_diferencia_cosecha_dia"];
      $valorSueldoMinimo = $fila["smin_inf"];

      $totalBonoBaseCalculo = $baseValorParaDiaLibre + $valorDiasLibres;

      $movimientoBaseCalculo = $cosechador->movimientosMensuales()
        ->getByTipoProduccion("BASE_CALCULO", $fecha)->first();

      if($movimientoBaseCalculo == null){

        $movimientoBaseCalculo = new MovimientoMensual();
        $movimientoBaseCalculo->fecha = $fecha;
        $movimientoBaseCalculo->tipo_no_produccion = "BASE_CALCULO";
        $movimientoBaseCalculo->cosechador_id = $cosechador->id;
      }

      $movimientoBaseCalculo->valor = $totalBonoBaseCalculo;
      $movimientoBaseCalculo->save();

      $movimientoBonoProduccionCalculado = $cosechador->movimientosMensuales()
        ->getByTipoProduccion("BONO_PRODUCCION_CALCULADO", $fecha)->first();

      if($movimientoBonoProduccionCalculado == null){
        $movimientoBonoProduccionCalculado = new MovimientoMensual();
        $movimientoBonoProduccionCalculado->fecha = $fecha;
        $movimientoBonoProduccionCalculado->tipo_no_produccion = "BONO_PRODUCCION_CALCULADO";
        $movimientoBonoProduccionCalculado->cosechador_id = $cosechador->id;
      }
      $movimientoBonoProduccionCalculado->valor = $bonoProdCalculado;
      $movimientoBonoProduccionCalculado->save();

      $movimientoVacaciones = $cosechador->movimientosMensuales()
        ->getByTipoProduccion("VV", $fecha)->first();

      if($movimientoVacaciones == null){
        $movimientoVacaciones = new MovimientoMensual();
        $movimientoVacaciones->fecha = $fecha;
        $movimientoVacaciones->tipo_no_produccion = "VV";
        $movimientoVacaciones->cosechador_id = $cosechador->id;
      }
      $movimientoVacaciones->valor = $valorVacaciones;
      $movimientoVacaciones->save();

      $movimientoDiasLibres = $cosechador->movimientosMensuales()
        ->getByTipoProduccion("LL", $fecha)->first();

      if($movimientoDiasLibres == null){
        $movimientoDiasLibres = new MovimientoMensual();
        $movimientoDiasLibres->fecha = $fecha;
        $movimientoDiasLibres->tipo_no_produccion = "LL";
        $movimientoDiasLibres->cosechador_id = $cosechador->id;
      }
      $movimientoDiasLibres->valor = $valorDiasLibres;
      $movimientoDiasLibres->save();

      $movimientoPermisoConGoce = $cosechador->movimientosMensuales()
        ->getByTipoProduccion("SS", $fecha)->first();

      if($movimientoPermisoConGoce == null){
        $movimientoPermisoConGoce = new MovimientoMensual();
        $movimientoPermisoConGoce->fecha = $fecha;
        $movimientoPermisoConGoce->tipo_no_produccion = "SS";
        $movimientoPermisoConGoce->cosechador_id = $cosechador->id;
      }
      $movimientoPermisoConGoce->valor = $valorDiasPermisos;
      $movimientoPermisoConGoce->save();
      $movimientoDiasNoProduccion = $cosechador->movimientosMensuales()
        ->getByTipoProduccion("AA", $fecha)->first();

      if($movimientoDiasNoProduccion == null){
        $movimientoDiasNoProduccion = new MovimientoMensual();
        $movimientoDiasNoProduccion->fecha = $fecha;
        $movimientoDiasNoProduccion->tipo_no_produccion = "AA";
        $movimientoDiasNoProduccion->cosechador_id = $cosechador->id;
      }
      $movimientoDiasNoProduccion->valor = $totalDiasNoProd;
      $movimientoDiasNoProduccion->save();

      $movimientoValorDiferenciaCosechaDia= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("VALOR_DIFERENCIA_COSECHA_DIA", $fecha)->first();

      if($movimientoValorDiferenciaCosechaDia == null){
        $movimientoValorDiferenciaCosechaDia = new MovimientoMensual();
        $movimientoValorDiferenciaCosechaDia->fecha = $fecha;
        $movimientoValorDiferenciaCosechaDia
          ->tipo_no_produccion = "VALOR_DIFERENCIA_COSECHA_DIA";
        $movimientoValorDiferenciaCosechaDia->cosechador_id = $cosechador->id;
      }
      $movimientoValorDiferenciaCosechaDia->valor = $valorDiferenciaCosechaDia;
      $movimientoValorDiferenciaCosechaDia->save();

      $movimientoValorSueldoMinimo= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("smin_inf", $fecha)->first();

      if($movimientoValorSueldoMinimo == null){
        $movimientoValorSueldoMinimo = new MovimientoMensual();
        $movimientoValorSueldoMinimo->fecha = $fecha;
        $movimientoValorSueldoMinimo
          ->tipo_no_produccion = "smin_inf";
        $movimientoValorSueldoMinimo->cosechador_id = $cosechador->id;
      }
      $movimientoValorSueldoMinimo->valor = $valorSueldoMinimo;
      $movimientoValorSueldoMinimo->save();
    }
}
