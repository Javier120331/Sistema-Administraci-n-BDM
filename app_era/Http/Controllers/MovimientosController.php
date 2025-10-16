<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use App\Utilidades\MovimientosUtils;
use App\Http\Requests\CrearMovimientoAsistenciaRequest;
use App\Empleado;
use App\Configuracion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use App\MovimientoAsistencia;
use App\MovimientoAsistenciaExport;
use PDF;
use App\Utilidades\FechaUtils;
use DataTables;
use Illuminate\Support\Collection;
use Response;
class MovimientosController extends Controller
{
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


    return view("movimientos.index");
  }

  public function getAjaxData(){
     $columns = 
     ['rut_empleado','nombre_empleado','tipo_texto'
     ,'area','fecha_inicio_movimientos','fecha_termino_movimientos'];

     $row = Input::get('start');
     $rowsPerPage = Input::get('length');
     $draw = Input::get('draw');
     $searchValue = Input::get('search')['value'];
     $columnSortOrder = Input::get('order')[0]['dir'];
     $columnIndex = $columns[Input::get('order')[0]['column']];
     
     //No incluimos los movimientos que son licencia medica en esta interfaz
     $cantidadMovimientos = MovimientoAsistenciaExport::where('tipo_movimiento', '!=', MovimientosUtils::TIPO_LICENCIA)->count();
     if($searchValue!= ""){
      $movimientos= MovimientoAsistenciaExport::where('nombre_empleado','like',"%$searchValue%")
        ->where('tipo_movimiento', '!=', MovimientosUtils::TIPO_LICENCIA)
        ->skip($row)->take($rowsPerPage)
        ->orderBy($columnIndex, $columnSortOrder)->get();
      $cantidadMovimientosFiltrados = MovimientoAsistenciaExport::where('nombre_empleado'
        ,'like',"%$searchValue%")
        ->where('tipo_movimiento', '!=', MovimientosUtils::TIPO_LICENCIA)
        ->count();
     } else {
      $movimientos= MovimientoAsistenciaExport::where('tipo_movimiento', '!=', MovimientosUtils::TIPO_LICENCIA)
        ->skip($row)->take($rowsPerPage)
        ->orderBy($columnIndex, $columnSortOrder)->get();
      $cantidadMovimientosFiltrados = MovimientoAsistenciaExport::count();
     }
     $movimientosProcesados = new Collection();
     for($i=0; $i< count($movimientos); ++$i){
       $movimiento = $movimientos[$i];
       $movimiento->fecha_inicio_movimiento_proc = $movimiento
        ->fecha_inicio_movimientos->format('d/m/Y');
        if($movimiento->fecha_termino_movimientos == null){
          $movimiento->fecha_termino_movimiento_proc = null;
        } else {
          $movimiento->fecha_termino_movimiento_proc = $movimiento
            ->fecha_termino_movimientos->format("d/m/Y");
        }

        $movimiento->tipo_texto = MovimientosUtils::$TIPOS_MOVIMIENTOS[$movimiento->tipo_movimiento];
        $movimientosProcesados[$i] = $movimiento;
     }
     $respuesta = array(
      'draw'=>intval($draw),
      "iTotalRecords"=>$cantidadMovimientos,
      "iTotalDisplayRecords"=> $cantidadMovimientosFiltrados,
      "aaData"=>$movimientosProcesados
     );
     return Response::json($respuesta);
  }

  public function create(){
     $empleadosDisponibles = Empleado::disponibles()->select(
         DB::raw("CONCAT(rut,' ', nombre) AS full_name, id")
       )->pluck('full_name','id');
     $tiposMovimientos = MovimientosUtils::$TIPOS_MOVIMIENTOS;
     return view("movimientos.create", compact('empleadosDisponibles'
        ,'tiposMovimientos'));
  }


  private function almacenarMovimientos($empleado
    , $fechaInicio, $cantidad, $tipo, $horaEntrada = null, $horaLlegada = null){
    $resultado = new \stdClass();
    $resultado->estado = true;
    $resultado->mensaje = null;
    $fechas = FechaUtils::getDiasHabiles($fechaInicio, $cantidad);
    $valido = MovimientosUtils::isFechasValidas($empleado
      ,$fechas, $tipo);

    if(!$valido){
      $resultado->estado= false;
      $resultado->mensaje = "Existen movimientos en el rango de fechas del mismo tipo";

    } else{
      if(in_array($tipo,MovimientosUtils::$TIPOS_MOVIMIENTOS_DESCARGA)){
        $descargable = 1;
      } else {
        $descargable = 0;
      }
      $resultado->idDocumento = $this->generarDocumento($empleado, $fechas
        , $tipo, $cantidad, $horaEntrada, $horaLlegada, $descargable);
      $idMovimientoExport = $resultado->idDocumento;
      $resultado->descargable = $descargable;
      MovimientosUtils::ingresarMovimientosEmpleado($empleado
        , $fechas, $tipo, $horaEntrada, $horaLlegada,null,null
        , $idMovimientoExport);
      $resultado->estado = true;
      $resultado->mensaje = "Ingreso de registros efectuado";

    }
    return $resultado;
  }


  private function generarDocumento($empleado
      , $fechas, $tipo, $cantidad, $horaEntrada = null, $horaLlegada = null
      , $descargable = 0){
    $fecha = Carbon::now();
    $fechaInicio = $fechas[0];
    $fechaTermino = $fechas[count($fechas)-1];
    $movimientoAsistenciaExport = new MovimientoAsistenciaExport();
    $movimientoAsistenciaExport->fecha_generacion = $fecha;
    $movimientoAsistenciaExport->titulo_documento = "Acuerdo de "
        .MovimientosUtils::$TIPOS_MOVIMIENTOS[$tipo];
    $movimientoAsistenciaExport->rut_empleado= $empleado->rut;
    $movimientoAsistenciaExport->nombre_empleado = $empleado->nombre;
    $movimientoAsistenciaExport->fecha_inicio_movimientos = $fechaInicio;
    $movimientoAsistenciaExport->cantidad_dias = $cantidad;
    if($fechaInicio->notEqualTo($fechaTermino)){
      $movimientoAsistenciaExport->fecha_termino_movimientos = $fechaTermino;
    }
    $movimientoAsistenciaExport->fecha_documento = $fecha;
    $movimientoAsistenciaExport->area = $empleado->area()->first()->nombre;
    $movimientoAsistenciaExport->tipo_movimiento = $tipo;
    $movimientoAsistenciaExport
      ->nombre_encargado = Configuracion::getByNombre('nombre_empleador')
      ->first()->valor;

    //Para atrasos
    $movimientoAsistenciaExport->hora_entrada = $horaEntrada;
    $movimientoAsistenciaExport->hora_llegada = $horaLlegada;
    $movimientoAsistenciaExport->descargable = $descargable;
    $movimientoAsistenciaExport->save();
    return $movimientoAsistenciaExport->id;
  }




  public function store(CrearMovimientoAsistenciaRequest $request){

     $fechaInicio = Carbon::createFromFormat("d/m/Y"
        ,Input::get("fecha_inicio"));
     $cantidad = Input::get("cantidad_dias");
     $tipo = Input::get("tipo");
     $horaEntrada = null;
     $horaLlegada = null;
     if($tipo == "AT"){
       $horaEntrada = Carbon::createFromFormat("H:i"
          ,Input::get("hora_entrada"));
        $horaLlegada = Carbon::createFromFormat("H:i"
          ,Input::get("hora_llegada"));
        //Si es atraso, solo se puede hacer con un día.
        //Así lo dejo definido YO.
        $cantidad = 1;
      }
     $empleado = Empleado::findOrFail(Input::get("empleado"))->first();
     $resultado = $this->almacenarMovimientos($empleado
      , $fechaInicio, $cantidad, $tipo, $horaEntrada, $horaLlegada);

     return redirect()->back()->with("resultado", $resultado)->withInput();
  }

  /**
   * Obtiene la vista encargada de renderizar el documento en base a su tipo
   * @param  String $tipo Tipo de movimiento (dentro de MovimientosUtils)
   * @return String  nombre de la view
   */
  private function getDownloadView($tipo){
    switch($tipo){
      case "PG": return "movimientos.pdfMovimientos_permiso_goce";
      break;
      case "PSG": return "movimientos.pdfMovimientos_permiso_sin_goce";
      break;
      case "IN" : return "movimientos.pdfMovimientos_faltas";
      break;
      case "AT": return "movimientos.pdfMovimientos_atrasos";
      break;
    }
    return "";
  }
  public function download($idDocumento){
    $movimiento = MovimientoAsistenciaExport::find($idDocumento);
    $nombreVista = $this->getDownloadView($movimiento->tipo_movimiento);
    $pdf = PDF::loadView($nombreVista, compact('movimiento'));
    return $pdf->stream();
    //return view("movimientos.pdfMovimientos", compact('movimiento'));
  }

  public function anular(){
    $movExp = MovimientoAsistenciaExport::findOrFail(Input::get('id'));
    $movExp->delete();
    $respuesta = new \stdClass();
    $respuesta->resultado = true;
    return Response::json($respuesta);
  }
}
