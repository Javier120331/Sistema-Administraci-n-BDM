<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Empleado;
use App\CausaFiniquito;
use App\Finiquito;
use App\Configuracion;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use App\DAO\FiniquitosService;
use App\Utilidades\NumerosUtils;
use App\Utilidades\VacacionesUtils;
use Response;
use DataTables;
use Illuminate\Support\Collection;
use App\Utilidades\Constantes;
use App\Http\Requests\FiniquitarPersonalRequest;
use PDF;
use Session;
use Illuminate\Support\MessageBag;
use App\Utilidades\FechaUtils;
use App\Utilidades\ImagenUtils;

class FiniquitosController extends Controller
{

    public function __construct()
    {
        //FORMATO DE moneda para Chile!

        $this->middleware('auth');
    }

    public function index()
    {
        return view("finiquitos.index");
    }

    public function getAjaxData()
    {
        $finiquitos= Finiquito::all();
        $finiquitosProcesados = new Collection();
        for ($i=0; $i< count($finiquitos); ++$i) {
            $finiquito = $finiquitos[$i];
            $finiquito->fecha_inicio_contrato_proc = $finiquito
      ->fecha_inicio_contrato->format('d/m/Y');
            $finiquito->fecha_finiquito_proc = $finiquito
      ->fecha_finiquito->format('d/m/Y');
            $finiquito->fecha_documento_proc = $finiquito
      ->fecha_documento->format('d/m/Y');
            $finiquitosProcesados->push($finiquito);
        }
        return DataTables::of($finiquitosProcesados)->make(true);
    }

    public function cargarDatosFiniquito()
    {
        if (Session::has('idEmpleado')) {
            $idEmpleado = Session::get('idEmpleado');
        } else {
            $idEmpleado = Input::get("empleado");
        }
        $empSel = Empleado::disponibles()->findOrFail($idEmpleado)->first();
        return $this->retornarVistaFiniquitos($empSel);
    }

    private function retornarVistaFiniquitos($empSel, $mensaje=null, $idFiniquito = null, $errors = null)
    {
        $empleadosDisponibles = Empleado::disponibles()->select(
        DB::raw("CONCAT(rut,' ', nombre) AS full_name, id")
    )->pluck('full_name', 'id');
        $causasDisponibles = CausaFiniquito::select(
          DB::raw("CONCAT(articulo,'-',numero,'-', descripcion) AS detalle, id")
      )->pluck('detalle', 'id');
        //solo si no ha sido finiquitado cargamos los datos del empleado
        if ($idFiniquito == null) {
            $sueldosFiniquito = $this
          ->formatearSueldoFiniquito(FiniquitosService::getUltimos3Meses($empSel->id));
            $resumen = $this->getResumenSueldos($empSel, $sueldosFiniquito);

            $datosServicio = $this->getAniosServicio($empSel, Carbon::now(), isset($resumen->promedioSinFormato) ?$resumen->promedioSinFormato: null) ;

            $causaSeleccionada = CausaFiniquito::first();
            Session::put("empSel", $empSel);
            //si hay errores de validación, sobrescribimos el objeto de errores
            if ($errors != null) {
                $empleado = $empSel->id;
                return redirect()->back()
            ->with('idEmpleado', $empSel->id)
            ->with('empSel', $empSel)
            ->withErrors($errors);
            } else {
                return view("finiquitos.create", compact('empleadosDisponibles', 'empSel', 'sueldosFiniquito', 'resumen', 'datosServicio', 'causasDisponibles', 'mensaje', 'causaSeleccionada'));
            }
        } else {
            Session::forget('empSel');
            return view("finiquitos.create", compact('empleadosDisponibles', 'causasDisponibles', 'mensaje', 'idFiniquito'));
        }
    }

    public function finiquitar(Request $request, $idEmpleado)
    {
        $validador = Validator::make($request->all(), FiniquitarPersonalRequest::rules(), FiniquitarPersonalRequest::messages())
        ;


        $empSel = Empleado::disponibles()->findOrFail($idEmpleado);
        //si el form no es valido, hasta aquí llegamos no mas
        if ($validador->fails()) {
            return $this->retornarVistaFiniquitos($empSel, null, null, $validador->messages());
        }
        $causaSel = CausaFiniquito::findOrFail(Input::get('causaFiniquito'))
        ->first();
        $fechaFiniquito = Carbon::createFromFormat('d/m/Y', Input::get('fecha_finiq'));
        $sueldoPromedio = NumerosUtils::unformatMoneda(Input::get('promedio_sueldo'));
        $aniosServicio = Input::get('anios_servicio');
        $aniosServicioTotal = NumerosUtils::unformatMoneda(Input::get('anios_servicio_total'));
        $pagarMesAviso = Input::get('pagar_mes_aviso_chk');
        $valorMesAviso = NumerosUtils::unformatMoneda(Input::get('mes_aviso_valor'));

        $finiquito = new Finiquito();
        $finiquito->empleado_id = $empSel->id;
        $finiquito->nombre_empleado = $empSel->nombre;
        $finiquito->rut_empleado = $empSel->rut;
        $finiquito->domicilio_empleado= $empSel->direccion;
        $finiquito->domicilio_empleador = Configuracion::getByNombre('domicilio_empleador')
        ->first()->valor;
        $finiquito->nombre_empleador = Configuracion::getByNombre('nombre_empleador')
        ->first()->valor;
        $finiquito->rut_empleador = Configuracion::getByNombre('rut_empleador')
        ->first()->valor;
        $finiquito->comuna_empleador = Configuracion::getByNombre('comuna_empresa')
        ->first()->valor;
        $finiquito->comuna_empleado= $empSel->comuna()->first()->nombre;
        $finiquito->nombre_empresa = Configuracion::getByNombre('nombre_empresa')
        ->first()->valor;
        $finiquito->comuna_empresa = Configuracion::getByNombre('comuna_empresa')
        ->first()->valor;
        $finiquito->rol_empresa = Configuracion::getByNombre('rol_empresa')
        ->first()->valor;
        $finiquito->cargo_empleado = $empSel->cargosEmpleado()
        ->first()->cargo()->first()->nombre;
        $finiquito->anios_servicio = Input::get('anios_servicio');
        $finiquito->remuneracion_promedio = NumerosUtils::unformatMoneda(
            Input::get('promedio_sueldo')
        );
        $finiquito->dias_inhabiles
          = Input::get('dias_inhabiles_vacaciones_txt');
        $finiquito->total_dias_inhabiles
          = NumerosUtils::unformatMoneda(
            Input::get('total_dias_inhabiles_vacaciones_txt'));
        $finiquito->descuento_sobregiro
          = NumerosUtils::unformatMoneda(
            Input::get('descuento_sobregiro_txt'));
        $finiquito->descuento_convenios
          = NumerosUtils::unformatMoneda(Input::get('descuento_convenios_txt'));
        if (Input::get('pagar_mes_aviso_chk')!==null) {
            $finiquito
            ->mes_aviso = NumerosUtils::unformatMoneda(
                Input::get('mes_aviso_valor')
            );
        }
        $finiquito
            ->dias_habiles_vacaciones = Input::get("dias_vacaciones_txt");
        $finiquito
            ->total_dias_habiles_vacaciones
            = NumerosUtils::unformatMoneda(
                Input::get("total_dias_vacaciones_txt")
            );

        $finiquito->indemnizacion_anios_servicio =
              $finiquito->remuneracion_promedio* $finiquito->anios_servicio;
        $finiquito->fecha_inicio_contrato = $empSel->fecha_inicio_contrato;
        $finiquito->fecha_finiquito = $fechaFiniquito;
        //TODO: Esta es la forma de definir la fecha de documento? averiguar
        $finiquito->fecha_documento = Carbon::now();
        $finiquito->causa_finiquito = $causaSel->descripcion;
        $finiquito->causa_finiquito_id = $causaSel->id;
        $finiquito->articulo_finiquito = $causaSel->articulo;
        $finiquito->numero_articulo_finiquito = $causaSel->numero;
        $finiquito->seguro_desempleo = NumerosUtils::unformatMoneda(
                  Input::get("seguro_desempleo_txt")
              );
        $finiquito->prestamo_empresa = NumerosUtils::unformatMoneda(
                    Input::get("prestamo_empresa_txt")
                );
        $finiquito->total_pagar = NumerosUtils::unformatMoneda(
                      Input::get("total_pagar_finiquito_txt")
                  );

        $finiquito->save();
        //Modificamos el estado del empleado a -1, que significa finiquitado
        $empSel->estado = Constantes::EMPLEADO_FINIQUITADO;
        $empSel->save();
        return $this->retornarVistaFiniquitos($empSel, 'Empleado Finiquitado', $finiquito->id);
    }

    public function download($id)
    {
        $finiquito = Finiquito::findOrFail($id);
        $imagen = ImagenUtils::getLogoEmpresa();
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true
                    , 'isRemoteEnabled' => true])->loadView("finiquitos.pdfFiniquito", compact('finiquito', 'imagen'));
        return $pdf->stream();
        //return view("finiquitos.pdfFiniquito", compact('finiquito','imagen'));
    }

    //@deprecated
    private function getTotalPagar($finiquito)
    {
        $total = 0;
        $total+= $finiquito->remuneracion_promedio;
        $total+= $finiquito->indemnizacion_anios_servicio;
        $total+= $finiquito->total_dias_habiles_vacaciones;
        $total+= $finiquito->seguro_desempleo;
        $total+= $finiquito->prestamo_empresa;

        return $total;
    }


    /**
    *
    * Obtiene los datos referentes a el pago de remuneraciones por concepto
    * de años de servicio
    * @param  Empleado $empleado  a quien se le requiere calcular los años
    * @param  Carbon $fechaFiniquito Fecha en la cual se genera el finiquito
    * (base para el cálculo de años de servicio)
    * @param  Integer $sueldo     Promedio de los ultimos 3 meses
    * (o en su defecto, valor proporcionado)
    * @return stdClass   incluye:
    *    - aniosServicio: años de servicio reales del empleado
    *    - totalPagarServicio: total a pagar por concepto de años de servicio
    */
    private function getAniosServicio($empleado, $fechaFiniquito, $sueldo)
    {
        $aniosTrabajados = $empleado->fecha_inicio_contrato
                    ->diffInYears($fechaFiniquito);

        $datos = new \stdClass();

        $datos->aniosServicio = $aniosTrabajados;
        //Solamente se pagan 11 años de servicio, no más
        if ($aniosTrabajados > 11) {
            $aniosTrabajados = 11;
        }
        if (isset($sueldo)) {
            $datos->totalPagarServicio
                      = NumerosUtils::getMoneda($sueldo * $aniosTrabajados);
        } else {
            $datos->totalPagarServicio = 0;
        }
        return $datos;
    }

    /**
     * Obtiene si de debe pagar años de servicio o no a empleado
     * , llamado desde create
     * @return {
     *    totalPagar: valorAPagar formateado para input
     * }
     */
    public function getAjaxSueldoAniosServicio(){

      $empleado = Session::has('empSel')?Session::get('empSel'): null;
      $resultado = new \stdClass();

      if($empleado != null){ // si es que fue almacenado en el método de finiquitos, sigo
        //sino hubo error de sesión
        $fechaFiniquito =Carbon::createFromFormat("d/m/Y"
          , Input::get('fechaFiniquito'));

        $sueldoPromedio = NumerosUtils::unformatMoneda(Input::get('sueldoPromedio'));
        $idCausaFiniquito =Input::get('idCausaFiniquito');
        $causaFiniquito = CausaFiniquito::find($idCausaFiniquito);

        //si se paga años calculamos los años y retornamos ese valor, sino null
        if($causaFiniquito->paga_anios == 1){
          $resultado = $this->getAniosServicio($empleado
            , $fechaFiniquito,$sueldoPromedio);
          } else {
            $resultado->totalPagarServicio = 0;
          }
      }
      return Response::json($resultado);
    }
    /**
    *
    * Genera un resumen de los datos para el finiquito del empleado
    * @param  Array $sueldosFiniquito  ultimos 3 sueldos del empleado
    * (sin considerar vacaciones ni licencias)
    * @return stdClass  incluye: -  Total ganado (suma de los 3 sueldos)
    *                            - Sueldo promedio de los 3 meses
    */
    private function getResumenSueldos($empleado, $sueldosFiniquito)
    {
        $resumen = new \stdClass();
        $fechaFiniquito =Carbon::now();
        $totalHaberesGanados = 0;
        for ($i=0; $i< count($sueldosFiniquito); ++$i) {
            $totalHaberesGanados+= $sueldosFiniquito[$i]->sueldo_total;
        }
        $resumen->total_ganado = NumerosUtils::getMoneda($totalHaberesGanados);
        if (count($sueldosFiniquito) > 0) {
            $resumen->promedioSinFormato =$totalHaberesGanados/count($sueldosFiniquito);
            $resumen->promedio
                      = NumerosUtils::getMoneda($resumen->promedioSinFormato);
        }
        $promedioRemu =isset($resumen->promedioSinFormato) ? $resumen->promedioSinFormato : 0;
        $resumen->valorVacaciones = VacacionesUtils::getValorVacacionesByEmp(
                        $empleado,
                        $promedioRemu,
                        $fechaFiniquito
                    );
        //Agregamos la cantidad de dias habiles e inhabiles iniciales
        $cantidadVacacionesDisp = VacacionesUtils::getDiasDisponiblesVacaciones($empleado,$fechaFiniquito);
        $resumen->diasHabilesVac = $cantidadVacacionesDisp;
        //Consideramos el día actual para el calculo
        $resumen->diasInhabilesVac
                        = count(FechaUtils::getDiasHabilesInhabiles(Carbon::now(), $cantidadVacacionesDisp)["inhabiles"]);

        $resumen->totalInhabilesVacaciones
                        = VacacionesUtils::getValorVacaciones(
                            $resumen->diasInhabilesVac,
                            $promedioRemu
                        );
        return $resumen;
    }

    /**
    * En base a un id de empleado y su remuneración promedio, devuelve
    * el valor correspondiente a pagar por conceptos de vacaciones
    * @return stdClass
    * {
    *   resultado: boolean indicando si fue exitoso o fracasó,
    *   valor: valor de las vacaciones a pagar (en pesos)
    * }
    */
    public function getValorVacacionesAjax()
    {
        $diasHabiles = Input::get('diasHabiles');
        $promedio = NumerosUtils::unformatMoneda(Input::get("remuneracionPromedio"));

        $respuesta = new \stdClass();
        $respuesta->resultado = true;
        $respuesta->valor = VacacionesUtils::getValorVacaciones($diasHabiles, $promedio);
        return Response::json($respuesta);
    }


    /**
    *
    * Genera el formato necesario para mostrar en la interfaz de Detalle
    * del finiquito.
    * @param  Array $sueldosFiniquito ultimos 3 sueldos del finiquito sin
    * considerar meses que incluyen licencias o vacaciones
    * @return Array  El mismo objeto pero agregando por cada indice
    *  - fecha_carbon: la fecha en formato español de tipo Carbon
    *  - sueldo_chile: corresponde al sueldo con formateo de moneda chileno
    *  (puntos en separadores de miles)
    */
    private function formatearSueldoFiniquito($sueldosFiniquito)
    {
        for ($i=0; $i< count($sueldosFiniquito); ++$i) {
            $sueldosFiniquito[$i]->fecha_carbon = Carbon::createFromFormat('Y-m-d', $sueldosFiniquito[$i]->fecha)->locale('es');
            $sueldosFiniquito[$i]->sueldo_chile
                        = NumerosUtils::getMoneda($sueldosFiniquito[$i]->sueldo_total);
        }
        return $sueldosFiniquito;
    }

    public function getAjaxDataEmpleado()
    {
        $idEmpleado = Input::get('idEmpleado');

        $empleado = Empleado::disponibles()->findOrFail($idEmpleado);
        $datos = new \stdClass();
        $resumenSueldos = $this
                      ->getResumenSueldos($empleado, FiniquitosService::getUltimos3Meses($empleado->id));
        $datos->sueldoActual="";
        if (isset($resumenSueldos->promedio)) {
            $datos->sueldoActual = $resumenSueldos->promedio;
        }
        $datos->empleado = $empleado;
        return Response::json($datos);
    }

    /**
     * En base a la fecha de finiquito y cantidad de dias habiles a pagar,
     * se calcula la cantidad de dias inhabiles y total a pagar
     * por concepto de ellos en el periodo
     * @return stdClass con estructura
     * {
     *     diasInhabiles: cantidad
     *   , totalInhabiles: valor
     * }
     */
    public function getAjaxInhabiles(){

      $fechaInicio = Carbon::createFromFormat('d/m/Y',Input::get('fecha_finiquito'));
      $cantidadDiasHabiles = Input::get('cantidad_habiles');
      $promedioRemu = Input::get('promedio_remu');
      $respuesta = new \stdClass();
      $respuesta->diasInhabiles = count(
            FechaUtils::getDiasHabilesInhabiles($fechaInicio
                        , $cantidadDiasHabiles)["inhabiles"]);

      $respuesta->totalInhabiles = VacacionesUtils::getValorVacaciones(
                          $respuesta->diasInhabiles,
                          $promedioRemu
                      );
     return Response::json($respuesta);
    }

    public function create()
    {
        $empleadosDisponibles = Empleado::disponibles()->select(
                          DB::raw("CONCAT(rut,' ', nombre) AS full_name, id")
                      )->pluck('full_name', 'id');
        return view("finiquitos.create", compact('empleadosDisponibles'));
    }


    public function anular()
    {
        DB::beginTransaction();
        $respuesta = new \stdClass();
        try {
            $finiquito= Finiquito::findOrFail(Input::get('id'));
            $idEmpleado = $finiquito->empleado_id;
            $finiquito->delete();

            $empleado = Empleado::findOrFail($idEmpleado);
            $empleado->estado = Constantes::EMPLEADO_HABILITADO;
            $empleado->save();
            $respuesta->resultado = true;
            DB::commit();
        } catch (\Exception $e) {
            $respuesta->resultado= false;
            DB::rollback();
        }
        return Response::json($respuesta);
    }
}
