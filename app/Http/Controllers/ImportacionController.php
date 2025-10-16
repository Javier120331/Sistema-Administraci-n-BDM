<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Utilidades\ImportacionProduccionUtils;
use App\Utilidades\Importacion\BaseProduccion;
use App\Utilidades\Importacion\BaseRemuneraciones;
use Response;
use DB;
use Carbon\Carbon;
use App\DAO\ProduccionService;
class ImportacionController extends Controller
{


  private $baseProduccion;
  private $baseRemuneraciones;
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth');
      $this->baseProduccion = new BaseProduccion();
      $this->baseRemuneraciones  = new BaseRemuneraciones();
  }

  public function index(){
    return view("importacion.index");
  }

  public function efectuarRollbackLiquidacion(){
      $fechaFin = Carbon::now();
      $fechaInicio = Carbon::now()->submonth();
      $fechaInicio->day = 21;
      $fechaFin->day = 20;
      ProduccionService::rollBack($fechaInicio, $fechaFin);
      return redirect()->back()->with('mensaje', 'Rollback efectuado');
  }
  public function subirArchivosRemuneraciones(){
    $respuesta = new \stdClass();

    $resultado = $this->baseRemuneraciones->iniciar();
    if($resultado){
      $resultadoProcesamiento = $this->baseRemuneraciones->procesarArchivos();
      if($resultadoProcesamiento->resultado){

        $this->baseRemuneraciones->moverTodoAHistorial();
        $respuesta->correcto = true;
        $respuesta->mensaje = "Importación exitosa";
      } else {
        $respuesta->correcto = false;
        $respuesta->mensaje = "No es posible procesar los archivos, Error: "
        .$resultadoProcesamiento->mensaje;
      }
    }else {
      $respuesta->correcto = false;
      $respuesta->mensaje =
        "Los archivos agregados no corresponden a la estructura";
    }
    return Response::json($respuesta);
  }


  public function subirArchivosLiquidacion(){
    $respuesta = new \stdClass();

    $resultado = $this->baseProduccion->iniciar();
    if($resultado){
      $resultadoProcesamiento = $this->baseProduccion->procesarArchivos();
      if($resultadoProcesamiento->resultado){
        $this->baseProduccion->moverTodoAHistorial();
        $respuesta->correcto = true;
        $respuesta->mensaje = "Importación exitosa";
      } else {
        $respuesta->correcto = false;
        $respuesta->mensaje = "No es posible procesar los archivos, Error: "
        .$resultadoProcesamiento->mensaje;
      }
    }else {
      $respuesta->correcto = false;
      $respuesta->mensaje =
        "Los archivos agregados no corresponden a la estructura";
    }
    return Response::json($respuesta);
  }
}
