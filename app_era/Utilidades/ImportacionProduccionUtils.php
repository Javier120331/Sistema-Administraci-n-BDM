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
use App\Imports\ProduccionesImport1;
use App\Imports\ProduccionesImport2;
use App\Imports\ProduccionesImport3;
use App\Imports\ProduccionesImport4;
/**
 * Clase encargada de efectuar las labores de importación de producción
 * de cosecha para Suelditas
 * @author SkyNux
 */
class ImportacionProduccionUtils extends ImportacionUtils{

    const COLUMNAS_NO_PRODUCCION = array(
      "valor_dias_de_vacaciones"
      ,"total_valor_dias_libres"
      ,"valor_dias_perm._cgs"
      , "valor_dias_de_vacaciones"
      , "total_valor_dias_libres"
      , "valor_dias_permiso_cgs"
      , "valor_cosecha_pago"
      , "bono_prod._calculado"
      , "bono_prod_calculado"
      , "base_valor_para_dia_libre"
      , "total_dias_no_produccion"
      , "total_valor_cosecha_pago"
      , "valor_minimo_diario_cosecha"
      , "valor_dias_perm._cgs"
      , "valor_dias_perm_cgs"
      , "valor_diferencia_cosecha_dia"
      , "smin_inf"
      , "valor_dias_no_produccion");

    //Tipos que deben ser descartados y no tomados
    //en cuenta (producto que hacen referencia a días inexistentes)
    const TIPOS_PRODUCCIONES_DESCARTADOS = array(
        "ZZ"
    );
    private  $directorioPadre = "Suelditas";
    private  $directorioLiquidaciones = "Suelditas/Liquidaciones";
    private  $directorioLiquidacionesHistorial = "Suelditas/Liquidaciones/.Historial";
    const CANTIDAD_ARCHIVOS = 4;
    const NOMBRE_INFORME = "TRASINFPRODU";


    public function iniciar(){
        return $this->start($this->directorioLiquidaciones
          , $this->directorioPadre
          , self::NOMBRE_INFORME, self::CANTIDAD_ARCHIVOS);
    }

    public function procesarArchivo2(){

      $archivo = $this->getArchivoByNro(2);
      $ruta = $archivo->getPathName();
      ProduccionesImport2::$fecha = $this->fecha;
      ProduccionesImport2::$delimitador= $this->delimitador;
      Excel::import(new ProduccionesImport2,$ruta, "publico");
      return true;
    }

    public function procesarArchivo3(){

      $archivo = $this->getArchivoByNro(3);
      $ruta = $archivo->getPathName();
      ProduccionesImport3::$fecha = $this->fecha;
      ProduccionesImport3::$delimitador= $this->delimitador;
      Excel::import(new ProduccionesImport3,$ruta, "publico");
      return true;
    }

    public function procesarArchivo4(){

      $archivo = $this->getArchivoByNro(4);
      $ruta = $archivo->getPathName();
      ProduccionesImport4::$fecha = $this->fecha;
      ProduccionesImport4::$anio = $this->anio;
      ProduccionesImport4::$mes = $this->mes;
      ProduccionesImport4::$delimitador= $this->delimitador;
      Excel::import(new ProduccionesImport4,$ruta, "publico");
      return true;

    }

    public function procesarArchivo1(){
      $archivo = $this->getArchivoByNro(1);
      $ruta = $archivo->getPathName();

      //Enviamos la fecha al contexto del import para poder
      //ingresar los días de producciones
      //Debemos hacerlo por la naturaleza del proceso de importación
      //de LaravelExcel
      //TODO: Se podrá pasar directamente al constructor?
      ProduccionesImport1::$fecha = $this->fecha;
      ProduccionesImport1::$delimitador= $this->delimitador;
      Excel::import(new ProduccionesImport1,$ruta, "publico");

      //TODO: Cuando devolver false?
      return true;
    }



    /**
     * Mueve todos los archivos al historial
     */
    public function moverTodoAHistorial(){

        $this->moverTodoAlHistorial($this->directorioPadre
          , $this->directorioLiquidaciones
          , $this->directorioLiquidacionesHistorial);
    }

    public function getCantidadArchivos(){
      return self::CANTIDAD_ARCHIVOS;
    }


}
