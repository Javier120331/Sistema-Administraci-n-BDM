<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
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
use App\MovimientoMensual;
use App\Utilidades\ImportacionProduccionUtils;
use App\Imports\ImportacionesBase;
class ProduccionesImport1 extends ImportacionesBase implements ToCollection
{
  public static $fecha;
  /**
  * @param Collection $collection
  */
  public function collection(Collection $collection)
  {
    $verificadoGenerales = false;
    foreach($collection as $fila){
      //solamente lo hacemos una vez, ya que las filas tienen
      //multiples veces el mismo valor
      if(!$verificadoGenerales){
        $this->procesarValoresCalidades($fila);
        $this->procesarValoresGenerales($fila);
        $verificadoGenerales = true;
      }
      $this->procesarRegistroArchivo1($fila);
    }
  }


  /**
  * Convierte el nombre de la calidad al existente en deFontana
  * @param  String $nombre en formato Veritas
  * @return String en lowerCase y con separador_
  */
  private function getNombreCalidadDeFontana($nombre){
    $nombreLower = strtolower($nombre);
    return str_replace(" ", "_",$nombreLower);
  }

  private function procesarProducciones($cosechador, $fila){

    $fechaAnterior = self::$fecha->copy()->subMonth();
    //empezamos con el mes anterior
    for($i=21; $i<= 31; ++$i){
      $diaProduccion = $fechaAnterior->copy();
      $diaProduccion->day = $i;
      $abrevTipoProduccion = $fila["movim_diario_dia_".$i];

      $tipoProduccion = TipoProduccion::getByAbreviacion($abrevTipoProduccion)
      ->first();
      if($tipoProduccion == null){
        $tipoProduccion = new TipoProduccion();
        $tipoProduccion->nombre = $abrevTipoProduccion;
        $tipoProduccion->abreviacion = $abrevTipoProduccion;

        //por defecto pagado
        $tipoProduccion->tipo_pago = 1;
        $tipoProduccion->save();
      }

      if(!in_array($tipoProduccion->abreviacion,ImportacionProduccionUtils::TIPOS_PRODUCCIONES_DESCARTADOS)){
        $produccion = Produccion::getByFechaAndIdCosechador($diaProduccion->toDateString()
          , $cosechador->id)
          ->first();
        //buscamos si la producción ya existe, y en ese caso, descartamos
        //el ingreso.
        if($produccion == null){
          $produccion = new Produccion();
          $produccion->cosechador_id = $cosechador->id;
          $produccion->tipo_produccion_id = $tipoProduccion->id;
          $produccion->numero_dia = $i;
          $produccion->fecha = $diaProduccion;
          $produccion->save();
        }
      }

    }


    //continuamos con el mes actual
    for($i=1; $i<= 20; ++$i){
      $diaProduccion = self::$fecha->copy();
      $diaProduccion->day = $i;
      $abrevTipoProduccion = $fila["movim_diario_dia_".$i];

      $tipoProduccion = TipoProduccion::getByAbreviacion($abrevTipoProduccion)
      ->first();
      if($tipoProduccion == null){
        $tipoProduccion = new TipoProduccion();
        $tipoProduccion->nombre = $abrevTipoProduccion;
        $tipoProduccion->abreviacion = $abrevTipoProduccion;

        //por defecto pagado
        $tipoProduccion->tipo_pago = 1;
        $tipoProduccion->save();
      }
      if(!in_array($tipoProduccion->abreviacion,ImportacionProduccionUtils::TIPOS_PRODUCCIONES_DESCARTADOS)){
          $produccion = Produccion::getByFechaAndIdCosechador($diaProduccion->toDateString()
            , $cosechador->id)
            ->first();
            //buscamos si la producción ya existe, y en ese caso, descartamos
          //el ingreso.
          if($produccion == null){
            $produccion = new Produccion();
            $produccion->cosechador_id = $cosechador->id;
            $produccion->tipo_produccion_id = $tipoProduccion->id;
            $produccion->numero_dia = $i;
            $produccion->fecha = $diaProduccion;
            $produccion->save();
          }
      }

    }
  }
  /**
  * Procesa los valores de producción /no producción incluidos en el archivo 1
  * @param  CellCollection $fila una fila del CSV 1
  * @return void
  */
  private function procesarValoresGenerales($fila){
    $valorMinimo  = $fila["valor_minimo_diario_cosecha"];
    $valorNoProduccion = $fila["valor_dias_no_produccion"];
    $configuracionMinimo = Configuracion::getByNombre(
      "valor_minimo_diario_cosecha")->first();
      //TODO: Una vez asegurado que el minimo de producción está calculado
        //TODO: por trabajador y no de forma general,se debe eliminar desde aquí
        //TODO: Esto considera que sea de forma general para todos
      if($valorMinimo != null){
        if($configuracionMinimo == null){
          $configuracionMinimo = new Configuracion();
          $configuracionMinimo->nombre = "valor_minimo_diario_cosecha";

        }
        $configuracionMinimo->valor = $valorMinimo;
        $configuracionMinimo->save();
      }
      $configuracionNoProduccion = Configuracion::getByNombre(
        "valor_dias_no_produccion")->first();
        if($valorNoProduccion != null){
          if($configuracionNoProduccion == null){
            $configuracionNoProduccion = new Configuracion();
            $configuracionNoProduccion->nombre = "valor_dias_no_produccion";
          }
          $configuracionNoProduccion->valor = $valorNoProduccion;
          $configuracionNoProduccion->save();
        }


      }

      /**
      * Procesa los valores de cada calidad incluidos en el archivo 1
      * @param  CellCollection $fila una fila del CSV 1
      * @return void
      */
      private function procesarValoresCalidades($fila){

        $calidadesVeritas = CalidadVeritas::all();
        for($i=0; $i< $calidadesVeritas->count(); ++$i){
          $calidadVeritaActual = $calidadesVeritas->get($i);
          $nombreDeFontana = $this
          ->getNombreCalidadDeFontana($calidadVeritaActual->nombre);

          $precioUnitario = $fila->get("precio_unitario_".$nombreDeFontana);

          //si es que es null, intentamos con la otra posibilidad
          //precio_unit_nombre
          //si, es flaite, pero así viene
          if($precioUnitario == null){
            $precioUnitario = $fila->get("precio_unit_".$nombreDeFontana);
          }

          //solo si la definición de precio de la calidad existe en el excel
          if($precioUnitario != null){
            $factorCalidad = $precioUnitario;
            $calidad = Calidad::getByCalidadVeritasId($calidadVeritaActual->id)
            ->first();
            if($calidad == null){
              $calidad = new Calidad();
              $calidad->id_calidad = $calidadVeritaActual->id;
            }
            $calidad->factor = $factorCalidad;

            //TODO: Necesitamos el campo valor?
            $calidad->valor = null;
            $calidad->save();
          }
        }
      }

      /**
      * TODO: Mejorar esto, actualmente se esta haciendo manualmente, pero
      * puede que sea mejor
      */
      private function procesarTotalesArchivo1($cosechador,$fila){
        $valorMinimoCosecha = $fila->get("valor_minimo_diario_cosecha");
        $movimientoMinimoCosecha= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("MINIMO_COSECHA", self::$fecha)->first();

        if($movimientoMinimoCosecha == null){

          $movimientoMinimoCosecha = new MovimientoMensual();
          $movimientoMinimoCosecha->fecha = self::$fecha;
          $movimientoMinimoCosecha->tipo_no_produccion = "MINIMO_COSECHA";
          $movimientoMinimoCosecha->cosechador_id = $cosechador->id;

        }

        $movimientoMinimoCosecha->valor = $valorMinimoCosecha;
        $movimientoMinimoCosecha->save();


        $totalDiasLibres = $fila->get("total_dias_libres");
        $movimientoTotalDiasLibres= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_DIAS_LIBRES", self::$fecha)->first();

        if($movimientoTotalDiasLibres == null){

          $movimientoTotalDiasLibres = new MovimientoMensual();
          $movimientoTotalDiasLibres->fecha = self::$fecha;
          $movimientoTotalDiasLibres->tipo_no_produccion = "TOTAL_DIAS_LIBRES";
          $movimientoTotalDiasLibres->cosechador_id = $cosechador->id;

        }

        $movimientoTotalDiasLibres->valor = $totalDiasLibres;
        $movimientoTotalDiasLibres->save();

        $valorTotalDiasLibres = $fila->get("total_valor_dias_libres");
        $movimientoTotalValorDiasLibres= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("VALOR_TOTAL_DIAS_LIBRES", self::$fecha)->first();

        if($movimientoTotalValorDiasLibres == null){

          $movimientoTotalValorDiasLibres = new MovimientoMensual();
          $movimientoTotalValorDiasLibres->fecha = self::$fecha;
          $movimientoTotalValorDiasLibres
          ->tipo_no_produccion = "VALOR_TOTAL_DIAS_LIBRES";
          $movimientoTotalValorDiasLibres->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorDiasLibres->valor = $valorTotalDiasLibres;
        $movimientoTotalValorDiasLibres->save();

          $precioUnitarioA = $fila->get("precio_unitario_bandeja_a");
        $movimientoPrecioUnitarioA= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("PRECIO_UNITARIO_BANDEJA_A", self::$fecha)->first();

        if($movimientoPrecioUnitarioA == null){

          $movimientoPrecioUnitarioA = new MovimientoMensual();
          $movimientoPrecioUnitarioA->fecha = self::$fecha;
          $movimientoPrecioUnitarioA
          ->tipo_no_produccion = "PRECIO_UNITARIO_BANDEJA_A";
          $movimientoPrecioUnitarioA->cosechador_id = $cosechador->id;

        }

        $movimientoPrecioUnitarioA->valor_double = $precioUnitarioA;
        $movimientoPrecioUnitarioA->save();

        $precioUnitarioB = $fila->get("precio_unitario_bandeja_b");
        $movimientoPrecioUnitarioB= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("PRECIO_UNITARIO_BANDEJA_B", self::$fecha)->first();

        if($movimientoPrecioUnitarioB == null){

          $movimientoPrecioUnitarioB = new MovimientoMensual();
          $movimientoPrecioUnitarioB->fecha = self::$fecha;
          $movimientoPrecioUnitarioB
          ->tipo_no_produccion = "PRECIO_UNITARIO_BANDEJA_B";
          $movimientoPrecioUnitarioB->cosechador_id = $cosechador->id;

        }

        $movimientoPrecioUnitarioB->valor_double = $precioUnitarioB;
        $movimientoPrecioUnitarioB->save();

        $precioUnitarioPG = $fila->get("precio_unitario_packing_granel");
        $movimientoPrecioUnitarioPG= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("PRECIO_UNITARIO_PACKING_GRANEL", self::$fecha)
        ->first();

        if($movimientoPrecioUnitarioPG == null){

          $movimientoPrecioUnitarioPG = new MovimientoMensual();
          $movimientoPrecioUnitarioPG->fecha = self::$fecha;
          $movimientoPrecioUnitarioPG
          ->tipo_no_produccion = "PRECIO_UNITARIO_PACKING_GRANEL";
          $movimientoPrecioUnitarioPG->cosechador_id = $cosechador->id;

        }

        $movimientoPrecioUnitarioPG->valor_double = $precioUnitarioPG;
        $movimientoPrecioUnitarioPG->save();

        $precioUnitarioPB = $fila->get("precio_unit_bandeja_portobello");
        $movimientoPrecioUnitarioPB= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("PRECIO_UNITARIO_BANDEJA_PORTOBELLO", self::$fecha)
        ->first();

        if($movimientoPrecioUnitarioPB == null){

          $movimientoPrecioUnitarioPB = new MovimientoMensual();
          $movimientoPrecioUnitarioPB->fecha = self::$fecha;
          $movimientoPrecioUnitarioPB
          ->tipo_no_produccion = "PRECIO_UNITARIO_BANDEJA_PORTOBELLO";
          $movimientoPrecioUnitarioPB->cosechador_id = $cosechador->id;

        }

        $movimientoPrecioUnitarioPB->valor_double = $precioUnitarioPB;
        $movimientoPrecioUnitarioPB->save();

        $precioUnitarioGPB = $fila->get("precio_unit_granel_portobello");
        $movimientoPrecioUnitarioGPB= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("PRECIO_UNITARIO_GRANEL_PORTOBELLO", self::$fecha)
        ->first();

        if($movimientoPrecioUnitarioGPB == null){

          $movimientoPrecioUnitarioGPB = new MovimientoMensual();
          $movimientoPrecioUnitarioGPB->fecha = self::$fecha;
          $movimientoPrecioUnitarioGPB
          ->tipo_no_produccion = "PRECIO_UNITARIO_GRANEL_PORTOBELLO";
          $movimientoPrecioUnitarioGPB->cosechador_id = $cosechador->id;

        }

        $movimientoPrecioUnitarioGPB->valor_double = $precioUnitarioGPB;
        $movimientoPrecioUnitarioGPB->save();

        $precioUnitarioPrimera = $fila->get("precio_unit_primera");
        $movimientoPrecioUnitarioPrimera =$cosechador->movimientosMensuales()
        ->getByTipoProduccion("PRECIO_UNITARIO_PRIMERA", self::$fecha)
        ->first();

        if($movimientoPrecioUnitarioPrimera== null){

          $movimientoPrecioUnitarioPrimera = new MovimientoMensual();
          $movimientoPrecioUnitarioPrimera->fecha = self::$fecha;
          $movimientoPrecioUnitarioPrimera
          ->tipo_no_produccion = "PRECIO_UNITARIO_PRIMERA";
          $movimientoPrecioUnitarioPrimera->cosechador_id = $cosechador->id;

        }

        $movimientoPrecioUnitarioPrimera->valor_double = $precioUnitarioPrimera;
        $movimientoPrecioUnitarioPrimera->save();

        $precioUnitarioSegunda= $fila->get("precio_unit_segunda");
        $movimientoPrecioUnitarioSegunda= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("PRECIO_UNITARIO_SEGUNDA", self::$fecha)
        ->first();

        if($movimientoPrecioUnitarioSegunda== null){

          $movimientoPrecioUnitarioSegunda = new MovimientoMensual();
          $movimientoPrecioUnitarioSegunda->fecha = self::$fecha;
          $movimientoPrecioUnitarioSegunda
          ->tipo_no_produccion = "PRECIO_UNITARIO_SEGUNDA";
          $movimientoPrecioUnitarioSegunda->cosechador_id = $cosechador->id;

        }

        $movimientoPrecioUnitarioSegunda->valor_double = $precioUnitarioSegunda;
        $movimientoPrecioUnitarioSegunda->save();

        $precioUnitarioEspecial= $fila->get("precio_unit_especial");
        $movimientoPrecioUnitarioEspecial= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("PRECIO_UNITARIO_ESPECIAL", self::$fecha)
        ->first();

        if($movimientoPrecioUnitarioEspecial== null){

          $movimientoPrecioUnitarioEspecial = new MovimientoMensual();
          $movimientoPrecioUnitarioEspecial->fecha = self::$fecha;
          $movimientoPrecioUnitarioEspecial
          ->tipo_no_produccion = "PRECIO_UNITARIO_ESPECIAL";
          $movimientoPrecioUnitarioEspecial->cosechador_id = $cosechador->id;

        }

        $movimientoPrecioUnitarioEspecial->valor_double = $precioUnitarioEspecial;
        $movimientoPrecioUnitarioEspecial->save();

        $valorDiasNoProduccion= $fila->get("valor_dias_no_produccion");
        $movimientoValorDiasNoProd= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("VALOR_DIAS_NO_PRODUCCION", self::$fecha)
        ->first();

        if($movimientoValorDiasNoProd== null){

          $movimientoValorDiasNoProd = new MovimientoMensual();
          $movimientoValorDiasNoProd->fecha = self::$fecha;
          $movimientoValorDiasNoProd
          ->tipo_no_produccion = "VALOR_DIAS_NO_PRODUCCION";
          $movimientoValorDiasNoProd->cosechador_id = $cosechador->id;

        }

        $movimientoValorDiasNoProd->valor = $valorDiasNoProduccion;
        $movimientoValorDiasNoProd->save();

        $totalValorBandejaA= $fila->get("total_valor_bandeja_a");
        $movimientoTotalValorBandejaA= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_BANDEJA_A", self::$fecha)
        ->first();

        if($movimientoTotalValorBandejaA== null){

          $movimientoTotalValorBandejaA = new MovimientoMensual();
          $movimientoTotalValorBandejaA->fecha = self::$fecha;
          $movimientoTotalValorBandejaA
          ->tipo_no_produccion = "TOTAL_VALOR_BANDEJA_A";
          $movimientoTotalValorBandejaA->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorBandejaA->valor = $totalValorBandejaA;
        $movimientoTotalValorBandejaA->save();

        $totalValorBandejaB= $fila->get("total_valor_bandeja_b");
        $movimientoTotalValorBandejaB= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_BANDEJA_B", self::$fecha)
        ->first();

        if($movimientoTotalValorBandejaB== null){

          $movimientoTotalValorBandejaB = new MovimientoMensual();
          $movimientoTotalValorBandejaB->fecha = self::$fecha;
          $movimientoTotalValorBandejaB
          ->tipo_no_produccion = "TOTAL_VALOR_BANDEJA_B";
          $movimientoTotalValorBandejaB->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorBandejaB->valor = $totalValorBandejaB;
        $movimientoTotalValorBandejaB->save();

        $totalValorPG= $fila->get("total_valor_packing_granel");
        $movimientoTotalValorPG= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_BANDEJA_B", self::$fecha)
        ->first();

        if($movimientoTotalValorPG== null){

          $movimientoTotalValorPG = new MovimientoMensual();
          $movimientoTotalValorPG->fecha = self::$fecha;
          $movimientoTotalValorPG
          ->tipo_no_produccion = "TOTAL_VALOR_PACKING_GRANEL";
          $movimientoTotalValorPG->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorPG->valor = $totalValorPG;
        $movimientoTotalValorPG->save();

        $totalValorPG= $fila->get("total_valor_packing_granel");
        $movimientoTotalValorPG= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_BANDEJA_B", self::$fecha)
        ->first();

        if($movimientoTotalValorPG== null){

          $movimientoTotalValorPG = new MovimientoMensual();
          $movimientoTotalValorPG->fecha = self::$fecha;
          $movimientoTotalValorPG
          ->tipo_no_produccion = "TOTAL_VALOR_PACKING_GRANEL";
          $movimientoTotalValorPG->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorPG->valor = $totalValorPG;
        $movimientoTotalValorPG->save();

        $totalValorBPB= $fila->get("total_valor_bandeja_portobello");
        $movimientoTotalValorBPB= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_BANDEJA_PORTOBELLO", self::$fecha)
        ->first();

        if($movimientoTotalValorBPB== null){

          $movimientoTotalValorBPB = new MovimientoMensual();
          $movimientoTotalValorBPB->fecha = self::$fecha;
          $movimientoTotalValorBPB
          ->tipo_no_produccion = "TOTAL_VALOR_BANDEJA_PORTOBELLO";
          $movimientoTotalValorBPB->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorBPB->valor = $totalValorBPB;
        $movimientoTotalValorBPB->save();

        $totalValorGPB= $fila->get("total_valor_granel_portobello");
        $movimientoTotalValorGPB= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_GRANEL_PORTOBELLO", self::$fecha)
        ->first();

        if($movimientoTotalValorGPB== null){

          $movimientoTotalValorGPB = new MovimientoMensual();
          $movimientoTotalValorGPB->fecha = self::$fecha;
          $movimientoTotalValorGPB
          ->tipo_no_produccion = "TOTAL_VALOR_GRANEL_PORTOBELLO";
          $movimientoTotalValorGPB->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorGPB->valor = $totalValorGPB;
        $movimientoTotalValorGPB->save();

        $totalValorPrimera= $fila->get("total_valor_primera");
        $movimientoTotalValorPrimera= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_PRIMERA", self::$fecha)
        ->first();

        if($movimientoTotalValorPrimera== null){

          $movimientoTotalValorPrimera = new MovimientoMensual();
          $movimientoTotalValorPrimera->fecha = self::$fecha;
          $movimientoTotalValorPrimera
          ->tipo_no_produccion = "TOTAL_VALOR_PRIMERA";
          $movimientoTotalValorPrimera->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorPrimera->valor = $totalValorPrimera;
        $movimientoTotalValorPrimera->save();

        $totalValorSegunda= $fila->get("total_valor_segunda");
        $movimientoTotalValorSegunda= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_SEGUNDA", self::$fecha)
        ->first();

        if($movimientoTotalValorSegunda== null){

          $movimientoTotalValorSegunda = new MovimientoMensual();
          $movimientoTotalValorSegunda->fecha = self::$fecha;
          $movimientoTotalValorSegunda
          ->tipo_no_produccion = "TOTAL_VALOR_SEGUNDA";
          $movimientoTotalValorSegunda->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorSegunda->valor = $totalValorSegunda;
        $movimientoTotalValorSegunda->save();

        $totalValorEspecial= $fila["total_valor_especial"];
        $movimientoTotalValorEspecial= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("TOTAL_VALOR_ESPECIAL", self::$fecha)
        ->first();

        if($movimientoTotalValorEspecial== null){

          $movimientoTotalValorEspecial = new MovimientoMensual();
          $movimientoTotalValorEspecial->fecha = self::$fecha;
          $movimientoTotalValorEspecial
          ->tipo_no_produccion = "TOTAL_VALOR_ESPECIAL";
          $movimientoTotalValorEspecial->cosechador_id = $cosechador->id;

        }

        $movimientoTotalValorEspecial->valor = $totalValorEspecial;
        $movimientoTotalValorEspecial->save();


      }


      private function procesarCosechador($fila){
        $rut = RutUtils::sanitizar($fila["codigo_empleado"]);
        $cosechador = Cosechador::getByRut($rut)->first();
        if($cosechador == null){
          $cosechador = new Cosechador();
          $cosechador->rut = $rut;
          $cosechador->nombre =$fila["nombre_completo"];
          $cosechador->fecha_contrato = Carbon::createFromFormat("d/m/Y"
          ,$fila["fecha_inicio_contrato"]);

          //Buscamos si el cosechador existe en veritas o no
          $trabajadorVeritas = Trabajador::getByRut($rut)->first();
          if($trabajadorVeritas == null){
            $cosechador->trabajador_id = null;
          } else {
            $cosechador->trabajador_id = $trabajadorVeritas->id;
          }
          $cosechador->save();
        }
        return $cosechador;
      }
      private function procesarRegistroArchivo1($fila){

        $cosechador = $this->procesarCosechador($fila);
        //@TODO Corroborar que esto sea así o ver como será..
        //Agregamos el valor mínimo de cosecha(debería estar en el archivo 4
        //, pero.. na que hacer)
        $this->procesarTotalesArchivo1($cosechador, $fila);
        $this->procesarProducciones($cosechador, $fila);
      }
    }
