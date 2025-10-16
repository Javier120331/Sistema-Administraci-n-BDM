<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Feriado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use App\Utilidades\FechaUtils;
class FeriadosController extends Controller
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

    public function getFechasRealesIncWeekend(){
        try{
          $fechaInicio = Carbon::createFromFormat("Y/m/d"
            ,Input::get("fecha_inicio"));
         }catch(\InvalidArgumentException $ex){
           $fechaInicio = Carbon::createFromFormat("d/m/Y"
             ,Input::get("fecha_inicio"));
         }
        $cantidad = Input::get("cantidad_dias");
        $fechas = FechaUtils::getDiasHabilesIncWeekend($fechaInicio, $cantidad);
        $resultado = new \stdClass();
        $resultado->fechas = $fechas;
        return Response::json($resultado);
      }

    public function getFechasReales(){
        try{
          $fechaInicio = Carbon::createFromFormat("Y/m/d"
            ,Input::get("fecha_inicio"));
         }catch(\InvalidArgumentException $ex){
           $fechaInicio = Carbon::createFromFormat("d/m/Y"
             ,Input::get("fecha_inicio"));
         }
        $cantidad = Input::get("cantidad_dias");
        $fechas = FechaUtils::getDiasHabilesString($fechaInicio, $cantidad);
        $resultado = new \stdClass();
        $resultado->fechas = $fechas;
        return Response::json($resultado);
      }

    public function store(){
      $fecha = Carbon::createFromFormat("d/m/Y"
        ,Input::get('fecha'));
      $descripcion = Input::get("descripcion");
      $respuesta = new \stdClass();
      $feriado = Feriado::find($fecha);
      if($feriado != null){
        $respuesta->resultado = false;
        $respuesta->mensaje = "Feriado ya existente";
      } else {
        $feriado = new Feriado();
        $feriado->fecha = $fecha;
        $feriado->descripcion = $descripcion;
        $feriado->extra = "";
        $feriado->save();
        $respuesta->resultado= true;
        $respuesta->mensaje = null;
      }
      return Response::json($respuesta);
    }


    public function delete(){
      $fecha = Carbon::createFromFormat("d/m/Y"
        ,Input::get('fecha'))->format('Y-m-d');

       Feriado::destroy($fecha);
       return "true";
    }

    public function getFeriados(){

      $anio = Input::get('anio');
      $feriados = null;
      if($anio == null){
        $feriados = Feriado::all();
      }else {

        $feriados = Feriado::getByAnio($anio)->get();
      }
      return Response::json($feriados);
    }

    public function index(){
      $anio = Carbon::now()->year;
      $anios = [];
      for ($i=($anio -2); $i <=$anio ; $i++) {
        $anios[$i]= $i;
      }
      return view("feriados.index", compact('anio', 'anios'));
    }

    public function importar(){
      $feriados = Input::get('feriados');
      $anio = Input::get('anio');
      return Response::json($this->procesarFeriados($feriados, $anio));
    }
    private function procesarFeriados($feriados, $anio){
      try{
        $resultado = new \stdClass();
        $resultado->resultado = true;
        DB::beginTransaction();
        for($i=0; $i< count($feriados); ++$i){
          $dato = $feriados[$i];
          $feriado = Feriado::find($dato["date"]);
          if($feriado == null){
            $feriado = new Feriado();
            $feriado->fecha = $dato["date"];
            $feriado->descripcion = $dato["title"];
            $feriado->extra = $dato["extra"];
            $feriado->save();
          }
        }
        DB::commit();
      }catch(Exception $ex){
        $resultado->resultado = false;
        $resultado->mensaje = null;
        DB::rollback();
      }

      return $resultado;
    }

}
