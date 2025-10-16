<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Cosechador;
use DB;
use App\Grupo;
use App\Trabajador;
use App\Http\Requests\GenerarLiquidacionRequest;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use \PhpOffice\PhpSpreadsheet\Style\Alignment;
use \PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use \PhpOffice\PhpSpreadsheet\Style\Conditional;
use \PhpOffice\PhpSpreadsheet\Style\Color;
use \PhpOffice\PhpSpreadsheet\Style\Border;
use \PhpOffice\PhpSpreadsheet\Shared\Font;
use \PhpOffice\PhpSpreadsheet\Writer\Xls;
use App\Configuracion;
use Response;
use App\CalidadVeritas;
use App\Calidad;
use App\DAO\ProduccionService;
use App\Utilidades\ProduccionUtils;
use App\MovimientoMensual;
class LiquidacionController extends Controller
{

  //TODO: este es el formato correcto?
  const FORMATO_NUMERO_CHILE = "_(#.##0_);_((#.##0);_(0??_);_(@_)";

  //atributos para generar la tabla de totales
  private $calidadesExistentes;
  private $totalKilosCalidades;
  private $totalesAPago;
  private $totalesDias;
  //Guarda el total por tipo de producción y cantidad de días
  //Ya que resulta que ahora los días HA y LL también tienen producción
  //Debe ser dividida por tipo de producción
  private $totalesValorPorTipoProduccion;
  //lo usamos para estandarizar el arreglo y reducir las querys
  private $calidadesSistema;
  private $calidadesVeritas;
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function index(){

    $trabajadoresDisponibles = Trabajador::trabajadoresDisponibles()->select(
        DB::raw("CONCAT(rut,' ', nombre) AS full_name, id")
      )->pluck('full_name','id');
    $gruposDisponibles = Grupo::gruposDisponibles()->pluck("nombre", "id");

    //Obtenemos el i del primer grupo de la lista, para obtener
    //los trabajadores de ese grupo
    $idPrimerGrupo = $gruposDisponibles->keys()->get(0);
    $grupo = Grupo::find($idPrimerGrupo);
    $valorTrabajadores = $grupo->trabajadores()
      ->trabajadoresDisponibles()->select(
        DB::raw("CONCAT(rut,' ', nombre) AS full_name, id")
      )->pluck('full_name','id');
    return view("liquidaciones.index"
      , compact("trabajadoresDisponibles", "gruposDisponibles", "valorTrabajadores"));
  }

  public function post(GenerarLiquidacionRequest $request){
    ini_set('max_execution_time', 500);
    $todosGrupos = Input::get('seleccionarTodos');
    $mes = Carbon::createFromFormat("m/Y",Input::get('mes'));
    if($todosGrupos == "todos"){

      return $this->generarLiquidacionTotal($mes);
    } else {

      $grupo = Grupo::findOrFail(Input::get("grupo"));
      $todosCosechadores = Input::get("seleccionarTodosPorGrupo");
      if($todosCosechadores == "todos"){
        if($grupo->trabajadores()->trabajadoresDisponibles()->count() > 0){
           return $this->generarLiquidacionTotal($mes, $grupo);
         } else {
           return redirect()->back()->with("mensaje"
             , "No existen cosechadores en el grupo seleccionado");
         }
      } else {

        $trabajadores = Trabajador::findOrFail(Input::get("cosechador"));
        $cosechadores = $this->getCosechadores($trabajadores);
        return $this->generarLiquidacionPorCosechadores($cosechadores, $mes);
      }
    }
  }

  /**
   * Agrega ancho automatico a todas las celdas de la hoja
   * @param  \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
   */
  private function definirAutoWidth($sheet){
    $sheet->calculateColumnWidths();
    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(true);
    /** @var \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
    foreach ($cellIterator as $cell) {
      $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
    }
  }

  /**
   * Define los estilos generales para toda la  hoja.
   * @param ExcelSheet $sheet Referencia a la hoja de Excel
   */
  private static function addEstilosGenerales($sheet, $tamanoLetra = null){
    if($tamanoLetra == null){
      $tamanoLetra = 11;
    }
    $sheet->getStyle($sheet->calculateWorksheetDimension() )
      ->applyFromArray(array(
      'font'=> array(
        'name' => 'Arial',
        'size' => $tamanoLetra
        )
      ));
  }

  private function generarLiquidacionTotal($mes, $grupo = null){
    if($grupo != null){

      $trabajadores= $grupo->trabajadores()->trabajadoresDisponibles()->get();
      $cosechadores = $this->getCosechadores($trabajadores);
      return $this->generarLiquidacionPorCosechadores($cosechadores, $mes);
    } else {
      $trabajadores = Trabajador::trabajadoresDisponibles()->get();
      $cosechadores = $this->getCosechadores($trabajadores);
      return $this->generarLiquidacionPorCosechadores($cosechadores, $mes);
    }

  }

  /**
   * En base a una lista de trabajadores, obtiene los cosechadores
   * @param  Collection(Trabajador) $trabajadores
   * @return Collection(Cosechador)
   */
  private function getCosechadores($trabajadores){
    $cosechadores = new Collection();
    $trabajadores->each(function($item, $key) use($cosechadores){
      $cosechador = $item->cosechador()->first();
      if($cosechador != null){
        $cosechadores->push($cosechador);
      }
    });
    return $cosechadores;
  }


  /**
  * Define el auto ancho de las celdas en excel
  **/
  private function startAutoSize(){
      \PhpOffice\PhpSpreadsheet\Shared\Font::setTrueTypeFontPath(app_path()."/fonts/");
      \PhpOffice\PhpSpreadsheet\Shared\Font::setAutoSizeMethod(\PhpOffice\PhpSpreadsheet\Shared\Font::AUTOSIZE_METHOD_EXACT);
  }


  private function generarLiquidacionPorCosechadores($cosechadores, $mes){
    $that = $this;
    $producciones = ProduccionService::getProduccionesCosechadores($cosechadores, $mes);
    $this->calidadesSistema = Calidad::all();
  //TODO: necesario?  $this->calidadesVeritas = CalidadVeritas::all();
    $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $excel->getProperties()
             ->setCreator("Bosques del Mauco")
             ->setTitle("Liquidación de Cosechadores")
             ->setLastModifiedBy("Bosques del Mauco")
             ->setDescription("Cartola de Cosecha")
             ->setSubject("Cartola de Cosechadores")
             ->setKeywords("exportacion cosecha cartola bosques del mauco")
             ->setCategory("Informes");
    $sheet = new Worksheet($excel, $mes->toDateString());
    $excel->addSheet($sheet, 0);
    $this->addEstilosGenerales($sheet);
    $posicionDatoActual = 10;
    for($i=0; $i< $cosechadores->count(); ++$i){
       set_time_limit(0);
      $that->addHojaCosechador($sheet, $cosechadores[$i], $mes, $posicionDatoActual, $producciones);
    }
    $this->startAutoSize();
    $this->definirAutoWidth($sheet);
    $nombreArchivo = "LiquidacionCosecha";
    $objWriter = new Xls($excel);
    $objWriter->save(storage_path()."/".$nombreArchivo.".xls");
    $headers = array('Content-Type: application/vnd.ms-excel',);
    return Response::download(storage_path()."/".$nombreArchivo.".xls"
      , $nombreArchivo.".xls"
      ,$headers)
      ->deleteFileAfterSend(true);
  }

  /**
   * Define borde a todas las celdas que se encuentran dentro del rango
   * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet hoja de Excel
   * @param String $rango rango con representación LetraNumero:LetraNumero
   */
  private function addBorder($sheet, $rango){
    $styleArray = array(
      'borders' => array(
        'allborders' => array(
          'style' => Border::BORDER_THIN
        )
      )
    );

    $sheet->getStyle($rango)->applyFromArray($styleArray);
  }

  /**
   * Agrega el formato de moneda chileno a un rango de celdas
   * @param PhpSpreadsheet $sheet
   * @param String $rango rango a definir
   */
  private function addFormatoNumero($sheet, $rango){
    $sheet->getStyle($rango)
      ->getNumberFormat()
      ->setFormatCode(LiquidacionController::FORMATO_NUMERO_CHILE);
  }

  /**
  * Agrega la hoja del cosechador específico en el Excel
  * @param  \PhpOffice\PhpSpreadsheet\Spreadsheet $excel
  * @param  Cosechador $cosechador
  * @param  Carbon\Carbon $mes  Mes del cual generar hoja
  * @param  int $posicion número de hoja
  * @param  producciones Corresponde a un arreglo de  objetos de la base de
  * datos conla siguiente estructura
  * [IdCosechador, TipoProduccion (Código),FechaProduccion
  *    ,kilos,factor
  *    , CalidadVeritas (IdDeCalidad)
  *    , IdCalidad]
  * ]
  */
  private function addHojaCosechador($sheet, $cosechador, $mes, &$posicion
    , $producciones){
    $thisActual = $this;
    //$thisActual->addLogo($sheet);
    $thisActual->setCellFontBold(array('D'.$posicion), $sheet, true);
    $sheet->fromArray(array("CARTOLA DE COSECHA"),null, "D".$posicion);
    $posicion+=2;
    $sheet->fromArray(array("Código:", $cosechador->id), null, "A".$posicion);
    ++$posicion;
    $sheet->mergeCells("B".$posicion.":E".$posicion);
    $sheet->mergeCells("G".$posicion.":H".$posicion);

    $grupo = "INDENIFIDO";
    if($cosechador->trabajador_id != null){
      $trabajador = $cosechador->trabajador();
      $grupoAux = $trabajador->grupo()->first();
      if($grupoAux != null){
        $grupo = $grupoAux->nombre;
      }
    }
    $sheet->fromArray(array("Nombre:", $cosechador->nombre, null, null, null
      , "Grupo:", $grupo)
      , null, "A".$posicion);
    ++$posicion;
    $sheet->fromArray(array("Rut:", $cosechador->rut
      , null, null,null, "Mes de Proceso:",null, $mes->toDateString()), null, "A".$posicion);
    $sheet->mergeCells("F".$posicion.":G".$posicion);
    $inicioBold = $posicion -2;
    $finBold = $posicion + 3;
    $sueldoMinimo = $cosechador->movimientosMensuales()
      ->getByTipoProduccion("smin_inf", $mes)->first();
    $thisActual->addDatosCosecha($sheet, $cosechador, $mes,$posicion
      , $producciones, $sueldoMinimo);
    $thisActual->setCellFontBold(array("A".$inicioBold.":AU".$finBold), $sheet);
    $thisActual->addTotalesCosecha($sheet, $posicion, $mes, $cosechador
      , $sueldoMinimo);
    $posicion+=10;
  }

  /**
   * Devuelve el total de produccion por tipo de dia del mes para el cosechadores
   * , exceptuando el NN
   * @return stdClass con estructura{
   *   TipoDia: Valor
   * }
   */
  private function getTotalesTipoDia($cosechador, $mes){
    $dias = array();
    //TODO: llamar a este metodo con el total de Dias
    foreach($this->totalesDias as $abreviacion => $total){
      if($abreviacion != "NN"){

        $movimientoMensual = $cosechador->movimientosMensuales()
          ->getByTipoProduccion($abreviacion,$mes)->first();
        if($movimientoMensual != null){
          $valorPagoPorDia = $movimientoMensual->valor;
        } else {
          $valorPagoPorDia = null;
        }
        $dia = new \stdClass();
        $dia->total = $total;
        $dia->valorPagoPorDia = $valorPagoPorDia;
        $dias[$abreviacion] = $dia;
      }
    }
    return $dias;
  }

  private function addTotalesCosecha($sheet, &$posicionInicial, $mes
    , $cosechador, $sueldoMinimo){
    $filasTotales = array();
    $totalesMensuales = $this->getTotalesTipoDia($cosechador, $mes);
    //agregamos algunos saltos de fila para que no salga pegado.
    $posicionInicial+=3;

    $totalAPagar = 0;
    for($i=0; $i < count($this->totalesAPago); ++$i){
      $totalAPagar+=$this->totalesAPago[$i];
    }


    $sheet->fromArray(array("Totales por Calidades", null, null, null
      , "Tipo de Dias",null, null,null
      ,null), null, "A".$posicionInicial);
    $i=0;
    self::setCellFontBold(array("A".$posicionInicial.":I".$posicionInicial), $sheet);
    $sheet->mergeCells("A".$posicionInicial.":B".$posicionInicial);
    $sheet->mergeCells("E".$posicionInicial.":F".$posicionInicial);
    $sheet->mergeCells("G".$posicionInicial.":H".$posicionInicial);
    ++$posicionInicial;
    $sheet->fromArray(array("Calidad", "Kilos","Factor", "$"), null, "A".$posicionInicial);
    self::setCellFontBold(array("A".$posicionInicial.":D".$posicionInicial), $sheet);
    $valorTotal=0;

    //TODO: Aquí agregar que se obtengan los totales desde deFontana
    //TODO: Verificar que va a pasar respecto a las nuevas calidades
    //TODO: En veritas, defontana y pesamatic, ya que tendría
    //TODO: que hacerse aquí de manera manual.
    $this->calidadesExistentes->each(function($calidad, $llave)
      use (&$sheet, &$i, &$filasTotales, &$valorTotal, $cosechador, $mes){

      $nombreCalidad = $calidad->calidadVeritas()->nombre;
      $movPrecioUnitario = $cosechador->movimientosMensuales()
          ->getPrecioUnitario($nombreCalidad,$mes)->first();
      $valor = round($this->totalKilosCalidades[$i] * $movPrecioUnitario->valor_double);
      $valorTotal+=$valor;
      array_push($filasTotales, array($nombreCalidad
        , $this->totalKilosCalidades[$i]
        , $movPrecioUnitario->valor_double
        , $valor));
      ++$i;
    });
    $i=0;

    //Obtenemos valor mínimo de cosecha
    $valorPagoAA = $sueldoMinimo != null? round($sueldoMinimo->valor/30) : 9600;

    foreach($this->totalesDias as $abreviacion => $total){
      //TODO: Averiguar para que es el HA
      if($abreviacion == "NN" || $abreviacion == "HA"){
          $valorPagoPorDia =  $this->totalesValorPorTipoProduccion[$abreviacion]->valorTotal;
      } else if($abreviacion == "AA"){
        $valorPagoPorDia = $total*$valorPagoAA;
      } else {
        $movimientoMensual = $totalesMensuales[$abreviacion];
        if($movimientoMensual != null){
          $valorPagoPorDia = $movimientoMensual->valorPagoPorDia;
        } else {
          $valorPagoPorDia = null;
        }
      }
      array_push($filasTotales[$i],null, "Día ".$abreviacion, $total, $valorPagoPorDia);
      ++$i;
      if($i >= count($filasTotales)){
        $i=0;
      }
    }

    ++$posicionInicial;
    for($i=0; $i < count($filasTotales); ++$i, ++$posicionInicial){
      $sheet->fromArray($filasTotales[$i], null, "A".$posicionInicial);
    }

    ++$posicionInicial;
    $totalBonoBaseCalculo =  $cosechador->movimientosMensuales()->getByTipoProduccion("BASE_CALCULO", $mes)->first();
    if($totalBonoBaseCalculo != null){
        $sheet->fromArray(array("TOTAL BONO BASE CALCULO",null,null, $totalBonoBaseCalculo->valor),null, "E".$posicionInicial);
    }

    $difMinimo = $cosechador->movimientosMensuales()
      ->getByTipoProduccion("VALOR_DIFERENCIA_COSECHA_DIA", $mes)->first();
    if($difMinimo != null && $difMinimo->valor != 0){
        $sheet->fromArray(array("DIF MINIMO",$difMinimo->valor),null,"A".($posicionInicial+1));
    }
    $bonoCalculadoPagar = $cosechador->movimientosMensuales()->getByTipoProduccion("BONO_PRODUCCION_CALCULADO", $mes)->first();
    if($bonoCalculadoPagar != null){
      $sheet->fromArray(array("TOTAL BONO CALCULADO A PAGAR",null,null, $bonoCalculadoPagar->valor),null, "E".($posicionInicial+1));
    }

    $this->addFormatoNumero($sheet, "C".$posicionInicial);
    $sheet->mergeCells("A".$posicionInicial.":B".$posicionInicial);
  }

  private function getDiasByProducciones($producciones){
    $totales = array();
    $produccionesAux = [];
    foreach($producciones as $produccion){
      $produccionesAux[$produccion->FechaProduccion] = $produccion;
    }
    foreach($produccionesAux as $produccion){
      $abreviacionActual = $produccion->TipoProduccion;

      //si existe el día en el cálculo, entonces aumentamos el valor
      //sino, lo definimos como el primer día
      if(array_key_exists($abreviacionActual, $totales)){
        $totales[$abreviacionActual]+=1;
      } else {
        $totales[$abreviacionActual] = 1;
      }
    }
    return $totales;
  }

  /**
   * Agrega logo a la hoja de Excel
   * @param ExcelSheet $sheet Referencia a la hoja de Excel.
   */
  private static function addLogo($sheet){
    $imagen = new Drawing;
    $imagen->setPath(public_path('img/logo.png'));
    $imagen->setCoordinates('A1');
    $imagen->setWorksheet($sheet);
  }

  private static function setCellFontBold($arregloCeldas, $sheet, $centerText = false){
    for($i=0; $i < count($arregloCeldas); ++$i){
      $celdaActual = $arregloCeldas[$i];
      //Significa que nos dieron un rango de celdas
      if(count(explode(':', $celdaActual))> 1){
        $sheet->getStyle($celdaActual)
              ->getFont()
              ->setBold(true);
        if($centerText){
          $sheet->getStyle($celdaActual)
                ->applyFromArray(array(
                  'alignment' => array(
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                  )));
        }
      } else {
        $sheet->getStyle($celdaActual)
              ->getFont()
              ->setBold(true);
        if($centerText){
          $sheet->getStyle($celdaActual)
                ->applyFromArray(array(
                  'alignment' => array(
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                  )));
        }
      }
    }
  }



  private function addDatosCosecha($sheet, $cosechador, $mes
    , &$posicionInicial, $producciones, $sueldoMinimo){

    $arregloSubTitulos = array("Tipo", null);
    $produccionesCosechador = ProduccionUtils::getProduccionesByCosechador($producciones, $cosechador);
    $calidadesExistentes = $this->getCalidadesByProducciones($produccionesCosechador);

    $arregloTotales = array("Valor por Cosecha:", null);
    $arregloTitulosProduccion = array("Fecha", "Dia");
    $totalesCalidades = array();
    $totalKilosCalidades = array();
    $i=0;
    $this->totalesAPago = array();
    //también pasamos las producciones para generar un arreglo con movimientos
    //de cada calidad para sacar totales
    $calidadesExistentes->each(function($item, $key) use(&$arregloSubTitulos
      , &$arregloTotales, &$arregloTitulosProduccion
      , &$i, &$totalesCalidades, &$totalKilosCalidades
      , $produccionesCosechador, $cosechador
      , $mes){
      $totalesCalidades[$i] = 0;
      $totalKilosCalidades[$i]=0;
      array_push($arregloSubTitulos, $item->calidadVeritas()->nombre
        , null);
      array_push($arregloTitulosProduccion, "Kilos","Valor");

      $produccionesCalidad = ProduccionUtils::getProduccionesByCalidad($produccionesCosechador, $item);
      $sumaKilos = 0;
      foreach($produccionesCalidad as $prodActual){
        $sumaKilos+=$prodActual->kilos;
      }
      //el precio unitario de la calidad, almacenada como movimiento mensual
      //es el factor por el cual multiplicar y calcular
      $movPrecioUnitario=$cosechador->movimientosMensuales()
        ->getPrecioUnitario($item->calidadVeritas()->nombre,$mes)->first();
      $totalKilosCalidades[$i]+=$sumaKilos;
      $total = $sumaKilos * $movPrecioUnitario->valor;
      $totalesCalidades[$i]+=round($total);
      $this->totalesAPago[$i] = round($total);
      ++$i;
    });
    //Lo agregamos como subtitulos para la fila final de totales.
    array_push($arregloTitulosProduccion, "Kilos", "Valor");
    for($i=0; $i < count($totalesCalidades); ++$i){
      array_push($arregloTotales, null, $totalesCalidades[$i]);
    }

    array_push($arregloSubTitulos,"Total");
    //aumentamos el valor de posición inicial para comenzar a arregar filas
    $posicionInicial++;
    $sheet->fromArray($arregloSubTitulos, null, "A".$posicionInicial);
    $posicionInicial++;
    $sheet->fromArray($arregloTotales, null, "A".$posicionInicial);
    $posicionInicial++;
    $sheet->fromArray($arregloTitulosProduccion, null, "A".$posicionInicial);
    $this->addDetallesProducciones($sheet, $cosechador, $produccionesCosechador, $mes
      , $calidadesExistentes, $posicionInicial, $sueldoMinimo);

    //guardamos este valor en los atributos para posteriormente agregar
    //la tabla de totales
    $this->totalKilosCalidades = $totalKilosCalidades;
    $this->calidadesExistentes = $calidadesExistentes;
  }

  private function addDetallesProducciones($sheet
    , $cosechador, $producciones, $mes, $calidadesExistentes
    , &$posicionInicial, $sueldoMinimo){

    $minimoCosecha= $cosechador->movimientosMensuales()
        ->getByTipoProduccion("MINIMO_COSECHA", $mes)->first();

    //Obtenemos valor mínimo de cosecha
    $valorPagoAA = $sueldoMinimo != null? round($sueldoMinimo->valor/30) : 9600;
    $this->totalesValorPorTipoProduccion = array();
    $this->totalesDias = $this->getDiasByProducciones($producciones);
    $this->totalesMensuales = $this->getTotalesTipoDia($cosechador, $mes);
    //comenzamos en el valor siguiente, porque el actual contiene lo del
    //proceso anterior
    $posicionInicial++;
    $fechasProducciones = [];
    foreach($producciones as $produccion){
      $fechasProducciones[$produccion->FechaProduccion] = $produccion;
    }

    foreach($fechasProducciones as $produccion){

      $fecha = Carbon::createFromFormat("Y-m-d",$produccion->FechaProduccion);
      $tipo = $produccion->TipoProduccion;
      if(!array_key_exists($tipo, $this->totalesValorPorTipoProduccion)){
          $this->totalesValorPorTipoProduccion[$tipo]= new \stdClass();
          $this->totalesValorPorTipoProduccion[$tipo]->cantidadDias = 0;
          $this->totalesValorPorTipoProduccion[$tipo]->totalKilos = 0;
          $this->totalesValorPorTipoProduccion[$tipo]->valorTotal = 0;
      }

      $this->totalesValorPorTipoProduccion[$tipo]->cantidadDias++;
      $arregloFila = array($fecha->format("d/m"),$tipo);
      $this->addFilaByCalidades($calidadesExistentes
        , $arregloFila, $produccion, $producciones);
      $totalKilosByFila = $this->getTotalKilosByFila($arregloFila);

      if($tipo == "AA"){
         $totalPesosByFila = $valorPagoAA;
      } else {
        $totalPesosByFila = $this->getTotalPesosByFila($arregloFila, $tipo);
      }

      //Solo para los días NN, ya que el minimo de cosecha se aplica
      //solo a los días productivos, o eso creo..
      //TODO: Verificar esto
      if($tipo == "NN" || $tipo== "HA"){
        if($minimoCosecha != null){
          if($totalPesosByFila < $minimoCosecha->valor){
            $totalPesosByFila = $minimoCosecha->valor;
          }
        }
      }

      //acumulamos los totales para la tabla final
      $this->totalesValorPorTipoProduccion[$tipo]->totalKilos += $totalKilosByFila;

      $this->totalesValorPorTipoProduccion[$tipo]->valorTotal += $totalPesosByFila;
      array_push($arregloFila
          , $totalKilosByFila);
      array_push($arregloFila
        , $totalPesosByFila);
      $sheet->fromArray($arregloFila, null, "A".$posicionInicial);
      ++$posicionInicial;
    }
  }

  /**
  * Obtiene el total de pesos en base a la fila de producción
  * @param   Array $arregloFila arreglo de fila con datos de producción
  * @return int total
  */
  private function getTotalPesosByFila($arregloFila, $abreviacion = "NN"){
    $total = 0;
    //TODO: Averiguar que diferencia tiene el día HA con el NN
     if($abreviacion == "NN" || $abreviacion == "HA"){
      for($i=3; $i < count($arregloFila); $i=$i+2){
        $total+=str_replace(",", ".",str_replace(".","",trim($arregloFila[$i])));
      }
    } else {
      //Si es AA, es un dia de no producción, se paga a 9600
      if($abreviacion == "AA"){
        $total = 9600;
      } else {
        $diaInhabil = $this->totalesMensuales[$abreviacion];
        if($diaInhabil != null){
          $total = $diaInhabil->valorPagoPorDia /$diaInhabil->total;
        }
      }
    }
    return round($total);
  }

  /**
  * Obtiene el total de kilos en base a la fila de producción
  * @param   Array $arregloFila arreglo de fila con datos de producción
  * @return int total
  */
  private function getTotalKilosByFila($arregloFila){

    $total = 0.0;

    for($i=2; $i < count($arregloFila); $i=$i+2){
      //desformateamos el números, para poder sumarlo
      $total+=$arregloFila[$i];
    }
    return $total;
  }

  private function addFilaByCalidades(&$calidadesExistentes, &$arregloFila
    ,$fechaProduccion, $producciones){

    $calidadesExistentes->each(function($calidadActual, $llave)
      use(&$fechaProduccion,$producciones,  &$arregloFila){

      $movimientos = ProduccionUtils::getProduccionesByFechaAndCalidad(
        $producciones, $fechaProduccion->FechaProduccion, $calidadActual);
      $totalKilos = 0;
      foreach($movimientos as $item){
          $totalKilos += $item->kilos;
      }
      $totalValor = round($totalKilos * $calidadActual->factor);
      array_push($arregloFila, $totalKilos
        , $totalValor);
    });
  }

  /**
   * Devuelve las calidades encontradas dentro de una colección de  producciones
   * @param  Collection(App\Produccion) $producciones
   * @return Collection(App\Calidad)
   */
  private function getCalidadesByProducciones($producciones){
    $calidades = array();

    foreach($producciones as $produccionActual){
      $calidad = $this->calidadesSistema->where("id"
        , (int)$produccionActual->IdCalidad)->first();
      $calidades[$produccionActual->IdCalidad] = $calidad;
    }
    //devolvemos una Collection
    return collect($calidades);
  }

  public function getCosechadoresByGrupo($idGrupo){
    $trabajadoresDisponibles = Grupo::find($idGrupo)
      ->trabajadores()->trabajadoresDisponibles()->select(
        DB::raw("CONCAT(rut,' ', nombre) AS text, id"))->get();
    return $trabajadoresDisponibles;
  }

}
