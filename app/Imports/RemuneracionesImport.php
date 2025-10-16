<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Imports\ImportacionesBase;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Utilidades\RutUtils;
use App\Utilidades\FechaUtils;
use App\Utilidades\Constantes;
use App\Utilidades\TiposBonosUtils;
use App\Bono;
use App\TipoBono;
use App\Empleado;
use App\SueldoBase;
use App\Comuna;
use App\Cargo;
use App\Area;
use App\CargoEmpleado;
use DB;
use Carbon\Carbon;


class RemuneracionesImport extends ImportacionesBase implements ToCollection
{
    public static $anio;
    public static $mes;
    public static $delimitador;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        try{
          $resultado = new \stdClass();
          $resultado->resultado = true;
          $resultado->mensaje = null;
          DB::beginTransaction();
          foreach($collection as $fila){

            $this->procesarFila($fila);
          }
          DB::commit();
        }catch(Exception $ex){
          $resultado->resultado = false;
          $resultado->mensaje = $ex->message;
          DB::rollback();
      }
      //TODO:que hacer con el resultado?

    }

    private function procesarFila($fila){

      $empleado = $this->getEmpleado($fila);
      $cargo = $this->getCargo($fila);
      $sueldoBase = $fila->get("sueldo_base");
      $fecha = Carbon::createFromDate(self::$anio, self::$mes, 1);
      $this->asignarCargoAEmpleado($empleado, $cargo, $fecha);
      
      $totalBonos = $this->procesarBonos($empleado,$fecha,$fila);
      $sueldoTotal = $sueldoBase + $totalBonos;
      $this->asignarSueldoEmpleado($empleado, $sueldoBase, $fecha, $sueldoTotal);

    }


    /**
     * Procesa los bonos existentes en la fila e ingresa en caso de ser
     * necesario
     * @param  Empleado $empleado
     * @param  Carbon $fecha
     * @param  Collection $fila
     */
    private function procesarBonos($empleado,$fecha,$fila){
      $totalBonos = 0;
      foreach(TiposBonosUtils::TIPOS_BONOS as $llave=> $texto){

          $valorBono = $fila->get($llave);
          //Solo si existe el valor, verificamos si existe el tipo
          //de bono creado
          if($valorBono != null){
            $tipoBono = $this->getTipoBono($llave);
            //si es que fue entregado un bono en base a lo descrito,
            //en ese caso ingresamos un registro
            //si el bono fue 0, no tiene sentido ingresar
            //TODO: este análisis es correcto?
            if($valorBono != 0){
              //Evaluamos si existe un bono para la fecha, del tipo
              //descrito, sin importar si es coincidente en valor o no
              $bonoBuscado = $empleado->bonos()
                  ->getByTipoAndFecha($tipoBono->id,$fecha)
                  ->first();
              //Si el bono no existe, entonces lo construimos
              if($bonoBuscado == null){
                $bonoBuscado = new Bono();
                $bonoBuscado->valor = $valorBono;
                $bonoBuscado->tipo_bono_id= $tipoBono->id;
                $bonoBuscado->empleado_id=$empleado->id;
                $bonoBuscado->fecha = $fecha;
              } else {
                //Si existe, entonces actualizamos su valor
                //TODO: Puede ocurrir esto? preguntar!
                $bonoBuscado->valor =$valorBono;
              }
              //Acumulamos los bonos para calcular el sueldo total
              $totalBonos+=$valorBono;
              $bonoBuscado->save();
            }
          }

      }
      return $totalBonos;
    }

    /**
     * Verifica si existe un tipo de bono creado, si no existe lo crea
     * @param  String $nombreBono nombre del bono (llave no texto)
     * @return TipoBono referencia al tipo de bono buscado
     */
    private function getTipoBono($nombreBono){
       $tipoBono = TipoBono::getByNombre($nombreBono)->first();
       if($tipoBono == null){
         $tipoBono = new TipoBono();
         $tipoBono->nombre = $nombreBono;
         $tipoBono->save();
       }
       return $tipoBono;
    }

    /**
     * Evalua si el sueldo proporcionado en base a la fecha es nuevo,
     * o ya existia, y efectúa la actualización del mismo en el sistema
     * @param  Empleado $empleado
     * @param  Integer $sueldoBase
     * @param  Carbon $fecha
     */
    private function asignarSueldoEmpleado($empleado, $valor, $fecha
      , $valorTotal){

      $sueldosBase = $empleado->sueldosBase();

      //Obtenemos el ultimo sueldo que cumple con el valor y fecha
      $sueldoBuscado = $sueldosBase->getByValorAndFecha($valor
        , $fecha)->orderBy('id', 'DESC')->first();
      //Solo efectuamos modificaciones si el sueldo no existe
      if($sueldoBuscado == null){

        $sueldoBuscado = new SueldoBase();
        $sueldoBuscado->valor = $valor;
        $sueldoBuscado->valor_total = $valorTotal;
        $sueldoBuscado->empleado_id = $empleado->id;
        $sueldoBuscado->fecha_asignacion = $fecha;
        $sueldoMasNuevo = $sueldosBase->getMasNuevoQue($fecha)->first();

        //si hay un sueldo más nuevo, entonces creamos el anterior
        //como un registro historico
        if($sueldoMasNuevo != null){
          $sueldoBuscado->estado= Constantes::SUELDO_HISTORICO;

        } else {
          //Si no, entonces debemos crearlo como el nuevo sueldo
          //y deshabilitar cualquier sueldo habilitado.
          $sueldoBuscado->estado = Constantes::SUELDO_ACTUAL;
          $sueldosBase->getSueldosActivos($fecha)
            ->update(["estado"=> Constantes::SUELDO_HISTORICO]);

        }
        $sueldoBuscado->save();
      }

    }

    /**
     * Evalúa si el cargo provisto debe ser agregado al empleado,
     * en el caso de que no exista dicho cargo dentro de los registros
     * lo crea con la fecha provista, y determina si el cargo
     * es el último del empleado o no.
     * @param  Empleado $empleado
     * @param  Cargo $cargo
     * @param  Carbon $fecha
     */
    private function asignarCargoAEmpleado($empleado, $cargo, $fecha){

      //Obtenemos los cargos que ya posee el empleado
      $cargosEmpleado = $empleado->cargosEmpleado();
      $cargoBuscado = $empleado->cargosEmpleado()
          ->getByCargoAndFecha($cargo->id, $fecha)->first();
      //si el cargo buscado nunca fue asignado, entonces
      //lo creamos.
      if($cargoBuscado == null){
        $cargoBuscado = new CargoEmpleado();
        $cargoBuscado->cargo_id = $cargo->id;
        $cargoBuscado->empleado_id= $empleado->id;
        $cargoBuscado->fecha_inicio_cargo = $fecha;
        $cargoMasNuevo =  $cargosEmpleado
          ->getMasNuevoQue($fecha)->orderBy('id', 'DESC')->first();

        //Si no existe un cargo más antiguo que este, actualizamos todos los
        //demas a deshabilitados y agregamos este como el último
        if($cargoMasNuevo == null){
          $cargosEmpleado
            ->getDeshabilitables($fecha)
            ->update(["estado"=> Constantes::CARGO_DESHABILITADO
                    , "fecha_termino_cargo"=>$fecha]);
          $cargoBuscado->estado = Constantes::CARGO_HABILITADO;
        } else {
            //sino, implica entonces que el  cargo almacenado debe ser
            //ingresado deshabilitado (ya que existe uno más relevante)

            $cargoBuscado->estado = Constantes::CARGO_DESHABILITADO;
            $cargoBuscado->fecha_termino_contrato = $fecha;

        }
        $cargoBuscado->save();

      }
    }

    /**
     * Evalua la existencia de un cargo en el sistema, si el cargo no
     * existe, crea uno nuevo con los datos del mismo
     * @param  Collection $fila fila del excel de Finiquitos
     * @return Cargo cargo al cual hace referencia la fila
     */
    private function getCargo($fila){
      $nombreCargo = strtoupper($fila->get("cargo_del_empleado"));
      $cargo = Cargo::getByNombre($nombreCargo)->first();
      if($cargo == null){
        $cargo = new Cargo();
        $cargo->nombre = $nombreCargo;
        $cargo->save();
      }
      return $cargo;
    }

    /**
     * Evalua la existencia del empleado en el sistema, si el empleado no
     * existe, crea uno nuevo con los datos del mismo
     * @param  Collection $fila fila del excel de Finiquitos
     * @return Empleado empleado al cual hace referencia la fila
     */
    private function getEmpleado($fila){
      $codigoEmpleado = $this->getCodigoEmpleado($fila);
      $rut = RutUtils::sanitizar($codigoEmpleado);
      $empleado = Empleado::getByRut($rut)->first();
      if($empleado == null){
        $empleado = new Empleado();
        $empleado->rut = $rut;
        $empleado->nombre = $fila->get("nombre_completo");
        $empleado->direccion = $fila->get("direccion");
        $empleado->comuna_id = $this->getComuna($fila)->id;
        $empleado->area_id  = $this->getArea($fila)->id;
        $empleado->fecha_inicio_contrato =  FechaUtils::getFechaCarbon(
          $fila->get("fecha_inicio_contrato")
          , "d/m/Y");
      }
      $empleado->fecha_termino_contrato =  FechaUtils::getFechaCarbon(
        $fila->get("fecha_termino_contrato")
        , "d/m/Y");
      $empleado->save();
      return $empleado;
    }

    /**
     * Evalua la existencia del area del empleadoen el sistema, si el area no
     * existe, crea una nueva con los datos de la fila
     * @param  Collection $fila fila del excel de Finiquitos
     * @return Area area al cual hace referencia la fila
     */
    private function getArea($fila){
        //siempre evaluamos la representación en mayusculas de la cadena
        //(y es la que almacenamos)
        $nombreArea = strtoupper($fila->get("descripcion_centro_de_negocios"));
        $area = Area::getByNombre($nombreArea)->first();
        if($area== null){
          $area = new Area();
          $area->nombre = $nombreArea;
          $area->save();
        }

        return $area;
    }

    /**
     * Evalua la existencia de la comuna en el sistema, si la comuna no
     * existe, crea una nueva con los datos de la fila
     * @param  Collection $fila fila del excel de Finiquitos
     * @return Comuna comuna al cual hace referencia la fila
     */
    private function getComuna($fila){
        //siempre evaluamos la representación en mayusculas de la cadena
        //(y es la que almacenamos)
        $nombreComuna = strtoupper($fila->get("comuna"));
        $comuna = Comuna::getByNombre($nombreComuna)->first();
        if($comuna == null){
          $comuna = new Comuna();
          $comuna->nombre = $nombreComuna;
          $comuna->save();
        }

        return $comuna;

    }


}
