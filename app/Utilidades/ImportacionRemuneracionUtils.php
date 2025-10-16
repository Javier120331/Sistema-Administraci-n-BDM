<?php
namespace App\Utilidades;
use File;
use Storage;
use Excel;
use Config;
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
use App\Utilidades\ImportacionUtils;
use App\Imports\RemuneracionesImport;
/**
 * Clase encargada de efectuar las labores de importación de remuneraciones
 *  y finiquitos de todos los trabajadores para Suelditas
 * @author SkyNux
 */
class ImportacionRemuneracionUtils extends ImportacionUtils{

  private  $directorioPadre = "Suelditas";
  private  $directorioLiquidaciones = "Suelditas/Remuneraciones";
  private  $directorioLiquidacionesHistorial = "Suelditas/Remuneraciones/.Historial";
  private  $campoDelimitador = "comuna";
  const CANTIDAD_ARCHIVOS = 1;
  const NOMBRE_INFORME = "TRASINFINIQ";
  public function iniciar(){
      return $this->start($this->directorioLiquidaciones
        , $this->directorioPadre
        , self::NOMBRE_INFORME, self::CANTIDAD_ARCHIVOS, $this->campoDelimitador);
  }


  public function procesarArchivoRemuneraciones(){
    RemuneracionesImport::$delimitador= $this->delimitador;
    $archivo = $this->archivos[0];
    $ruta = $archivo->getPathName();
    RemuneracionesImport::$anio = $this->anio;
    RemuneracionesImport::$mes = $this->mes;
    Excel::import(new RemuneracionesImport,$ruta, 'publico');
    //TODO: Recorrer esta shit!
    //TODO: Cuando devolver false?
    $resultado = new \stdClass();
    $resultado->resultado = true;
    $resultado->mensaje = null;
    return $resultado;
  }

  /**
   * Mueve todos los archivos al historial
   */
  public function moverTodoAHistorial(){

      $this->moverTodoAlHistorial($this->directorioPadre
        , $this->directorioLiquidaciones
        , $this->directorioLiquidacionesHistorial);
  }


}
