<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\VacacionProgresiva;
use App\Http\Requests;
use DB;
use App\Empleado;
use App\Vacacion;
use App\Periodo;
use App\Utilidades\MovimientosUtils;
use App\Utilidades\FechaUtils;
use App\Utilidades\VacacionesUtils;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;
use DataTables;
use PDF;
use App\Utilidades\ImagenUtils;
use App\DAO\PeriodosService;
use App\DAO\VacacionesService;
class VacacionesController extends Controller
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


    public function consulta(){
      $empleadosQuery = Empleado::disponibles();
      $empleadosDisponibles = $this->getEmpleados($empleadosQuery);
      return view("vacaciones.consulta", compact('empleadosDisponibles'));
    }

    public function create(){
      $empleadosQuery = Empleado::disponibles();
      $empleadosDisponibles = $this->getEmpleados($empleadosQuery);
      return view("vacaciones.create", compact('empleadosDisponibles'));
    }

    public function store(){

       $fechaInicio = Carbon::createFromFormat("d/m/Y"
          ,Input::get("fecha_inicio"));
       $fechaFinal = Carbon::createFromFormat("d/m/Y"
             ,Input::get("fecha_termino"));
       $cantidad = Input::get("cantidad_dias");
       $nombrePeriodo = Input::get('periodo')[0];
       $empleado = Empleado::findOrFail(Input::get("empleado"))->first();
       $respuesta = new \stdClass();
       $respuesta->estado = false;
       $respuesta->mensaje = null;
       $respuesta->vacacion = null;
       DB::beginTransaction();
       try{
         //Efectuamos ingreso del registro de vacaciones
         $vacacion = new Vacacion();
         $periodo = PeriodosService::getPeriodo($empleado,$nombrePeriodo);
         $vacacion->periodo_id = $periodo->id;
         $vacacion->fecha_inicio = $fechaInicio;
         $vacacion->fecha_termino = $fechaFinal;
         $vacacion->cantidad_dias = $cantidad;
         //TODO: Se debe ingresar manualmente la fecha de solicitud?
         $vacacion->fecha_registro = Carbon::now();
         $vacacion->saldo_periodo = $periodo->dias_disponibles;
         $vacacion->dias_ya_autorizados = $periodo->dias_autorizados;
         $periodo->dias_autorizados += $vacacion->cantidad_dias;
         $periodo->dias_disponibles-=$vacacion->cantidad_dias;
         $vacacion->saldo_pendiente_periodo = $periodo->dias_disponibles;
         $periodo->save();
         $vacacion->save();

         $respuesta = $this->almacenarMovimientos($empleado
         , $fechaInicio, $cantidad, MovimientosUtils::TIPO_VACACIONES
         , $vacacion->id);

         if($respuesta->estado){
            DB::commit();
            $respuesta->vacacion = $vacacion;
         } else {
            DB::rollBack();
         }

       }catch(\Illuminate\Database\QueryException $ex){
           DB::rollBack();
           if($ex->getCode()== 1062){
             $respuesta->mensaje = "Ya existe una licencia con ese folio";
           } else {
             $respuesta->mensaje = "Excepcion no controlada";
           }

       }catch(Exception $ex){
           DB::rollBack();
           $respuesta->mensaje = "Excepcion ocurrida ".$ex->getMessage();
       }
       return redirect()->back()
          ->with("resultado", $respuesta)->withInput();
    }

    public function download($idVacacion){
      //WORKAROUND para solucionar el problema de generacion de pdf en
      //ultima versión de PHP
      error_reporting(E_ALL ^ E_DEPRECATED);
      $vacacion = Vacacion::find($idVacacion);
      $periodo= $vacacion->periodo()->first();
      $imagen = ImagenUtils::getLogoEmpresa();
      $resumen = $this->getDatosResumenVacaciones($vacacion);
      $pdf = PDF::loadView("vacaciones.pdfVacaciones", compact('vacacion','imagen'
        ,'resumen','periodo'));
      return $pdf->stream();
      //return view("vacaciones.pdfVacaciones", compact('vacacion','imagen'
      //  ,'resumen','periodo'));
    }

    /**
     * Devuelve los datos de saldo pendiente de vacaciones para el pdf de resultado
     * @param  Vacacion $vacacion
     * @return stdClass
     */
    private function getDatosResumenVacaciones($vacacion){
        $resumen = new \stdClass();
        $periodo = $vacacion->periodo()->first();
        $empleado = $periodo->empleado()->first();
        $resumen->dias_base = VacacionesUtils::CANTIDAD_DIAS_BASE_VACACIONES;
        $resumen->dias_feriado_progresivo
          = VacacionesUtils::getDiasFeriadoProgresivo($empleado
            ,$vacacion->fecha_inicio);
        $resumen->saldos_al_periodo = $vacacion->saldo_periodo;
        $resumen->saldo_pendiente = $vacacion->saldo_pendiente_periodo;

        $resumen->fecha_retorno = FechaUtils::getDiasHabiles(
            $vacacion->fecha_termino,2)[1];
        return $resumen;
    }

    /**
     * @DEPRECATED
     * @param  [type] $vacacion [description]
     * @param  [type] $empleado [description]
     * @return [type]           [description]
     */
    private function getDatosPeriodo($vacacion,$empleado){
       $datosPeriodo = new \stdClass();
       $fechaContrato = $empleado->fecha_inicio_contrato;

       $datosPeriodo->fecha_inicio = $fechaContrato->copy();
       $datosPeriodo->fecha_inicio->year = $vacacion->fecha_inicio->year - 1;
       $datosPeriodo->fecha_termino = $fechaContrato->copy();
       $datosPeriodo->fecha_termino->year =  $vacacion->fecha_inicio->year;

       //TODO: Verificar si esta es la forma correcta de calcular
       //TODO: Esto se está haciendo para calcular el periodo
       if($vacacion->fecha_inicio > $datosPeriodo->fecha_termino){
         $datosPeriodo->fecha_inicio->year=$vacacion->fecha_inicio->year;
         $datosPeriodo->fecha_termino->year=$vacacion->fecha_inicio->year + 1;
       }
       $datosPeriodo->dias_ya_autorizados = $this
        ->getDiasYaAutorizados($datosPeriodo);
       return $datosPeriodo;
    }
    /**
     * @DEPRECATED
     * @param  [type] $periodo [description]
     * @return [type]          [description]
     */
    private function getDiasYaAutorizados($periodo){
        $vacaciones =
          Vacacion::getEnRangoInicio($periodo->fecha_inicio
          , $periodo->fecha_termino)->get();
        $cantidadDias=0;
        foreach($vacaciones as $vacacion){
            $cantidadDias+=$vacacion->cantidad_dias;
        }
        return $cantidadDias;
    }
    private function almacenarMovimientos($empleado
      , $fechaInicio, $cantidad, $tipo, $idVacaciones){
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
        MovimientosUtils::ingresarMovimientosEmpleado($empleado
          , $fechas, $tipo, null,null, $idVacaciones);
        $resultado->estado = true;
        $resultado->mensaje = "Registro de vacaciones efectuado";
        if(in_array($tipo,MovimientosUtils::$TIPOS_MOVIMIENTOS_DESCARGA)){
          $resultado->idDocumento = $this->generarDocumento($empleado, $fechas
            , $tipo, $cantidad, $horaEntrada, $horaLlegada);
        } else {
          $resultado->idDocumento = -1;
        }
      }
      return $resultado;
    }

    private function getEmpleados($empleadosQuery){
      $empleadosDisponibles = $empleadosQuery->select(
          DB::raw("CONCAT(rut,' ', nombre) AS full_name, id")
        )->pluck('full_name','id');
      return $empleadosDisponibles;
    }


    public function getAjaxDataPeriodos(){
        return $this->getPeriodos(Input::get('idEmpleado'));
    }

    private function getPeriodos($idEmpleado){
      $empleado = Empleado::findOrFail($idEmpleado);
      $periodos = PeriodosService::getPeriodos($empleado)
        ->pluck('nombre','nombre');
      return $periodos;
    }
    /**
     * En base a una fecha de inicio, entrega la cantidad de dias
     * que le corresponden al trabajador para vacaciones, considerando:
     *  - 1.25 * cada mes
     *  - el periodo que lleva de vacaciones en el año actual
     * @return JSON con la cantidad de días
     * @Deprecated
     */
    public function getCantidadDiasRecomendadosOld(){
      $fechaInicio = Carbon::createFromFormat("d/m/Y"
         ,Input::get("fecha_inicio"));
      $empleado = Empleado::disponibles()->findOrFail(Input::get("empleado"));

      $resultado= new \stdClass();
      $resultado->permitirVacaciones = true;
      $resultado->mensaje = null;
      $fechaInicial=null;

      $diasRecomendados = VacacionesUtils
        ::getDiasDisponiblesVacaciones($empleado);
      if($diasRecomendados <= 0){
          $resultado->permitirVacaciones = false;
          $resultado->mensaje
            = "El Trabajador no tiene días de vacaciones disponibles";
      }else {
        $resultado->diasRecomendados = $diasRecomendados;
      }
      return Response::json($resultado);
    }


    public function getCantidadDiasRecomendados(){
      $nombrePeriodo = Input::get("nombre_periodo");
      $idEmpleado = Input::get('id_empleado');

      $empleado = Empleado::find($idEmpleado);
      $periodo= PeriodosService::getPeriodo($empleado,$nombrePeriodo);
      if($periodo != null){
        $diasRecomendados = $periodo->dias_disponibles;
      } else {
        $diasRecomendados = 15; //TODO: VER COMO CALCULAR REALMENTE ESTO
      }

      $resultado= new \stdClass();
      $resultado->permitirVacaciones = true;
      $resultado->mensaje = null;


      if($diasRecomendados <= 0){
          $resultado->permitirVacaciones = false;
          $resultado->mensaje
            = "El Trabajador no tiene días de vacaciones disponibles";
      }else {
        $resultado->diasRecomendados = $diasRecomendados;
      }
      return Response::json($resultado);
    }

    public function index(){

      return view("vacaciones.index");
    }
    public function indexProgresivas(){

      return view("vacaciones.indexProgresivas");
    }
    public function getAjaxData(){
        $columns=[
          'rut_empleado',
          'nombre_empleado',
          'fecha_registro',
          'fecha_inicio',
          'cantidad_dias',
          'fecha_termino'
        ];
        $row = Input::get('start');
        $rowsPerPage = Input::get('length');
        $draw = Input::get('draw');
        $searchValue = Input::get('search')['value'];
        $columnSortOrder = Input::get('order')[0]['dir'];
        $columnIndex = $columns[Input::get('order')[0]['column']];
        
        $cantidadVacaciones = VacacionesService::getCantidadVacaciones();
       
        $cantidadVacacionesFiltrados = VacacionesService::getCantidadVacaciones($searchValue,$row,$rowsPerPage, $columnIndex,$columnSortOrder);
        $vacacionesProcesadas = VacacionesService::getVacaciones($searchValue,$row,$rowsPerPage, $columnIndex,$columnSortOrder);
          
       
       

      $respuesta = array(
        'draw'=>intval($draw),
        "iTotalRecords"=>$cantidadVacaciones,
        "iTotalDisplayRecords"=> $cantidadVacacionesFiltrados,
        "aaData"=>$vacacionesProcesadas
      );
      return Response::json($respuesta);
    }

    public function getAjaxDataEmpleado(){
      $idEmpleado = Input::get('idEmpleado');
      $respuesta = new \stdClass();
      $empleado = Empleado::findOrFail($idEmpleado);

      $respuesta->empleado = $empleado;
      $respuesta->progresivas = $empleado->vacacionesProgresivas()->first();

      $respuesta->cargo = $empleado->getCargoActual()
        ->first()->cargo()->first();

      return Response::json($respuesta);
    }
    public function getAjaxDataConsulta(){
       $idEmpleado = Input::get('idEmpleado');
       $empleado = Empleado::findOrFail($idEmpleado);
       $progresivas = $empleado->vacacionesProgresivas()->first();
       $periodos = PeriodosService::getPeriodos($empleado);
       $periodosProcesados = new Collection();
       for($i=0; $i< count($periodos); ++$i){
         $periodo = $periodos[$i];

         $periodo->base = VacacionesUtils::CANTIDAD_DIAS_BASE_VACACIONES;

         $periodo->progresivas = VacacionesUtils::getDiasFeriadoProgresivo(
           $empleado, $periodo->fecha_termino);
         $periodosProcesados->push($periodo);
       }


       return DataTables::of($periodosProcesados)->make(true);
    }
    public function getAjaxDataProgresivas(){

       $vacacionesProgre= VacacionProgresiva::all();
       $vacacionesProcesadas = new Collection();
       for($i=0; $i< count($vacacionesProgre); ++$i){
         $vacacion = $vacacionesProgre[$i];
         $vacacion->rut_empleado = $vacacion->empleado()->first()->rut;
         $vacacion->fecha_proc = $vacacion->fecha
          ->format('d/m/Y');
         $vacacion->nombre_empleado = $vacacion->empleado()->first()->nombre;
         $vacacionesProcesadas->push($vacacion);
       }
       return DataTables::of($vacacionesProcesadas)->make(true);
    }
    public function agregarVacacionesProgresivas(){
      $empleadosQuery = Empleado::disponibles();
      $empleadosDisponibles = $this->getEmpleados($empleadosQuery);
      return view("vacaciones.createProgresivas", compact('empleadosDisponibles'));
    }

    public function storeVacacionesProgresivas(){
      $respuesta = new \stdClass();
      $respuesta->estado = true;

      $fechaInicio = Carbon::createFromFormat("d/m/Y"
         ,Input::get("fecha"));
      $aniosEmpresa = Input::get("anios_empresa");
      $empleado = Empleado::findOrFail(Input::get("empleado"))->first();
      //Si el empleado ya tiene vacaciones progresivas registradas
      //no se puede volver a registrar!
      if($empleado->vacacionesProgresivas()->count() > 0){
        $respuesta->estado = false;
        $respuesta->mensaje = "El trabajador ya posee vacaciones progresivas"
        ." registradas";
      } else{
        $fechaInicioContrato = $empleado->fecha_inicio_contrato;



        if($fechaInicio->diffInYears($fechaInicioContrato) >=3){

                $vacacionProgre = new VacacionProgresiva();
                $vacacionProgre->empleado_id = $empleado->id;
                $vacacionProgre->fecha = $fechaInicio;
                $vacacionProgre->cantidad_anios = $aniosEmpresa;
                $vacacionProgre->save();
                $respuesta->estado = true;
                $respuesta->mensaje = "Registro de vacaciones progresivas efectuado exitosamente";
              } else {
                $respuesta->estado = false;
                $respuesta->mensaje = "El trabajador debe al menos haber"
                ." trabajado en la empresa por 3 años para generar"
                ." vacaciones progresivas";
              }
      }
      return redirect()->back()->with("resultado", $respuesta)->withInput();
    }

    public function anular(){
      DB::beginTransaction();
      $respuesta = new \stdClass();
      $respuesta->resultado = true;
      try{
      $vacacion = Vacacion::findOrFail(Input::get('id'));
      //Recuperamos los días proporcionados al periodo asociado
      $periodo = $vacacion->periodo()->first();
      $periodo->dias_disponibles+= $vacacion->cantidad_dias;
      $periodo->dias_autorizados-= $vacacion->cantidad_dias;
      $periodo->save();
      $vacacion->delete();
      DB::commit();
      }catch(\Illuminate\Database\QueryException $ex){
          dd($ex);
          DB::rollBack();
          $respuesta->resultado = false;

      }catch(Exception $ex){
          dd($ex);
          DB::rollBack();
          $respuesta->resultado=false;
      }
      return Response::json($respuesta);
    }


}
