<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\CausaLicencia;
use App\Licencia;
use App\Empleado;
use App\Configuracion;
use App\MovimientoAsistenciaExport;
use DB;
use Illuminate\Support\Collection;
use DataTables;
use App\Utilidades\MovimientosUtils;
use App\Utilidades\LicenciaUtils;
use App\Utilidades\FechaUtils;
use Carbon\Carbon;
use App\DAO\LicenciasService;
use Response;
class LicenciasController extends Controller
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

    $causasLicencias = CausaLicencia::all()->pluck('nombre','id');
    $tiposLicencias = collect(LicenciaUtils::$TIPOS_LICENCIAS);
    return view("licencias.index", compact('causasLicencias','tiposLicencias'));
  }

  public function create(){
    $empleadosDisponibles = Empleado::disponibles()->select(
      DB::raw("CONCAT(rut,' ', nombre) AS full_name, id")
      )->pluck('full_name','id');
      $causasLicencias = CausaLicencia::all()->pluck('nombre','id');

      $tiposLicencias = collect(LicenciaUtils::$TIPOS_LICENCIAS);
      return view("licencias.create"
      , compact('empleadosDisponibles'
      , 'causasLicencias'
      ,'tiposLicencias'));
    }

    private function detenerOtrosMovimientos($empleado
    , $fechaInicio, $fechaTermino){

      DB::beginTransaction();
      try{
        //detenemos movimientos que no sean vacaciones
        LicenciasService::detenerMovimientosConflictivos($empleado
        ,$fechaInicio,$fechaTermino);
        //detenemos las vacaciones del trabajador existentes
        //$query=
        $periodos = $empleado->periodos()->getEnRango($fechaInicio,$fechaTermino)->get();
        foreach($periodos as $periodo){
          $vacacionesConflictivas = $periodo->vacaciones()
          ->getEnRango($fechaInicio,$fechaTermino)->get();
          $vacacionesConflictivas;
          //$sql = str_replace_array('?', $query->getBindings(), $query->toSql()); dd($sql);
          foreach($vacacionesConflictivas as $vc){
            if($vc->fecha_inicio->gte($fechaInicio)
            && $vc->fecha_inicio->lte($fechaTermino)){
              //hay que eliminar las vacaciones cuando parten dentro
              //de una licencia!
              $periodo->dias_autorizados-= $vc->cantidad_dias;
              $periodo->dias_disponibles+= $vc->cantidad_dias;
              $periodo->save();
              Vacacion::destroy($vc->id);

            } else if($vc->fecha_termino->gte($fechaInicio)
            || $vc->fecha_termino->lte($fechaTermino)) {
              //Restamos los días faltantes de vacaciones y los sumamos a los disponibles
              //del periodo
              $diasFaltantes = FechaUtils::getDiffDias($vc->fecha_termino, $fechaInicio);
              $periodo->dias_disponibles+=$diasFaltantes;
              $periodo->dias_autorizados-=$diasFalantes;
              //En el caso de que la fecha de termino esté en el
              //rango de la licencia, se actualiza la fecha e término!
              $vc->fecha_termino = $fechaInicio;
              LicenciasService::detenerVacaciones($empleado
              ,$fechaInicio,$fechaTermino, $vc->id);
              $vc->save();
              $periodo->save();
            }
          }
        }
        DB::commit();
      } catch (\Exception $e) {
        $respuesta->resultado= false;
        DB::rollback();
      }
    }

    public function store(){
      $licencia = new Licencia();
      $empleado = Empleado::disponibles()->findOrFail(Input::get('empleado'))->first();
      $causa = Input::get("causa");
      $cantidadDias  = Input::get("cantidad_dias");
      $fechaInicio = Carbon::createFromFormat("d/m/Y"
      ,Input::get("fecha_inicio"))->startOfDay();
      $fechaTermino = Carbon::createFromFormat("d/m/Y"
      ,Input::get("fecha_final"))->endOfDay();
      $folio = Input::get("folio");
      $respuesta = new \stdClass();
      $respuesta->estado = false;
      $respuesta->mensaje = null;
      DB::beginTransaction();
      try{
        //detenemos movimientos existentes entre las fechas de la licencia
        $this->detenerOtrosMovimientos($empleado,$fechaInicio, $fechaTermino);
        $licencia->empleado_id = $empleado->id;
        $licencia->causa_licencia_id = $causa;
        $licencia->fecha_inicio = $fechaInicio;
        $licencia->cant_dias = $cantidadDias;
        $licencia->fecha_termino = $fechaTermino;
        $licencia->folio_licencia = $folio;
        $licencia->tipo = Input::get("tipo");
        $licencia->save();
        $respuesta = $this->almacenarLicencia($empleado
        ,$licencia->fecha_inicio
        , $licencia->cant_dias
        , $licencia->fecha_termino
        , $licencia->id);
        if($respuesta->estado){
          DB::commit();
        } else {
          DB::rollBack();
        }

      }catch(\Illuminate\Database\QueryException $ex){

        DB::rollBack();

        if($ex->getCode()== 23000){
          $respuesta->mensaje = "Ya existe una licencia con ese folio";
        } else {
          $respuesta->mensaje = "Excepcion no controlada";
        }
      }catch(Exception $ex){

        DB::rollBack();
        $respuesta->mensaje = "Excepcion ocurrida ".$ex->getMessage();
      }
      return redirect()->back()->with("resultado", $respuesta)->withInput();
    }

    private function almacenarLicencia($empleado
    , $fechaInicio, $cantidad, $fechaTermino,$idLicencia){
      $resultado = new \stdClass();
      $resultado->estado = true;
      $resultado->mensaje = null;
      $fechas = FechaUtils::getDiasHabilesIncWeekend($fechaInicio, $cantidad);
      $valido = MovimientosUtils::isFechasValidas($empleado
      ,$fechas, MovimientosUtils::TIPO_LICENCIA);

      if(!$valido){
        $resultado->estado= false;
        $resultado->mensaje = "Existen movimientos en el rango de fechas del mismo tipo";

      } else{
        MovimientosUtils::ingresarMovimientosEmpleado($empleado
        , $fechas, MovimientosUtils::TIPO_LICENCIA, null, null,null,$idLicencia);
        $resultado->estado = true;
        $resultado->mensaje = "Ingreso de registros efectuado";
        
        $resultado->idDocumento = -1;
        
        $this->generarDocumento($empleado, $fechas
          ,MovimientosUtils::TIPO_LICENCIA, $cantidad, null, null);
        
      }
      return $resultado;
    }

    public function getAjaxData(){
      $estado = Input::get("estado",1);
      $causa =  Input::get("causa","");
      $tipo =  Input::get("tipo","");
      $causa = $causa ==""?null:$causa;
      $tipo = $tipo ==""?null:$tipo;
      $licencias= Licencia::getByEstadoAndCausaTipo($estado,$causa,$tipo)->get();
      $licenciasProcesadas = new Collection();
      for($i=0; $i< count($licencias); ++$i){
        $licencia = $licencias[$i];
        $licencia->causa_licencia = $licencia->causa()->first()->nombre;
        $licencia->rut_empleado = $licencia->empleado()->first()->rut;
        $licencia->nombre_empleado = $licencia->empleado()->first()->nombre;
        $licencia->fecha_inicio_proc = $licencia->fecha_inicio->format('d/m/Y');

        $licencia->fecha_termino_proc = $licencia
        ->fecha_termino->format("d/m/Y");

        $licencia->tipo_licencia = LicenciaUtils::$TIPOS_LICENCIAS[$licencia->tipo];
        $licenciasProcesadas->push($licencia);
      }
      return DataTables::of($licenciasProcesadas)->make(true);
    }

    private function generarDocumento($empleado
    , $fechas, $tipo, $cantidad, $horaEntrada = null, $horaLlegada = null
    , $descargable = 0){

      $fecha = Carbon::now();
      $fechaInicio = $fechas[0];
      $fechaTermino = $fechas[count($fechas)-1];
      $movimientoAsistenciaExport = new MovimientoAsistenciaExport();
      $movimientoAsistenciaExport->fecha_generacion = $fecha;
      $movimientoAsistenciaExport->titulo_documento = MovimientosUtils::$TIPOS_MOVIMIENTOS[$tipo];
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
    
      $movimientoAsistenciaExport->save();
      return $movimientoAsistenciaExport->id;
    }

    public function anular(){
      $licencia = Licencia::findOrFail(Input::get('id'));
      $licencia->delete();
      $respuesta = new \stdClass();
      $respuesta->resultado = true;
      return Response::json($respuesta);
    }
  }
