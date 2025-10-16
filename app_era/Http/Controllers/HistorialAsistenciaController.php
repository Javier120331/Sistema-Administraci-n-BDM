<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empleado;
use Illuminate\Support\Facades\Input;
use DB;
use Illuminate\Support\Collection;
use App\MovimientoAsistenciaExport;
use App\Utilidades\MovimientosUtils;
class HistorialAsistenciaController extends Controller
{
    public function __construct(){
                //FORMATO DE moneda para Chile!

        $this->middleware('auth');
    }

    public function index()
    {
        $empleadosDisponibles = Empleado::disponibles()->select(
                          DB::raw("CONCAT(rut,' ', nombre) AS full_name, rut")
                      )->pluck('full_name', 'rut');
        return view("historial_asistencia.index", compact('empleadosDisponibles'));
    }
  
    public function generar(){

     $rutEmpleado = Input::get('empleado');
     $empleadosDisponibles = Empleado::disponibles()->select(
                          DB::raw("CONCAT(rut,' ', nombre) AS full_name, rut")
                      )->pluck('full_name', 'rut');

    $movimientos= MovimientoAsistenciaExport::getByRutEmpleado($rutEmpleado)->orderBy('fecha_inicio_movimientos','DESC')->get();
     
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
      return view("historial_asistencia.index", compact('empleadosDisponibles', 'rutEmpleado', 'movimientosProcesados'));
  }
}
