<?php
namespace App\Utilidades;

use File;
use Storage;
use Excel;
use Config;
use Carbon\Carbon;
use App\Imports\VerificarSeparadorImport;
class ImportacionUtils{

  protected $delimitador= ",";
  //Posibles delimitadores de fichero CSV que puede usar deFontana
  const DELIMITADORES_POSIBLES = array( ",",";");
  protected $mes;
  protected $anio;

  //representación Carbon de la relación mes/año
  protected $fecha;
  protected $archivos;


  public function __construct(){
    $this->archivos = array();
  }



  /**
   * Obtiene un nuevo fichero a procesar verificando que no exista
   * en el arreglo de archivos
   * @param $directorio Directorio desde donde obtener el fichero
   * @param $archivos arreglo dentro del cual descartar coincidencias de nombre
   * default array()
   * @return File o null en caso de error
   */
  public function getFicheroNuevo($directorio, $directorioPadre, $archivos = array()){

    //Siempre verificar si existe directorioPadre
    self::verificarYCrearDirectorio($directorioPadre);
    self::verificarYCrearDirectorio($directorio);
    $listaArchivos = File::files($directorio);
    foreach($listaArchivos as $archivo){
      $encontrado = array_first($archivos, function ($value, $key) use($archivo) {

        return File::name($value) == File::name($archivo);
      }, false);

      if(File::mimeType($archivo) == "text/plain" && !$encontrado){
        return $archivo;
      }
    }
    return null;
  }
  /**
   * Iniciar el proceso de importacion, identificando los
   * archivos requeridos
   * @param $directorio  Corresponde al directorio directo donde se encuentran
   * los ficheros de importación
   * @param $directorioPadre Corresponde al directorio padre del directorio
   * padre directo de los archivos
   * @param $cantidadArchivos corresponde a la cantidad de archivos a
   * procesar
   * @return boolean true en caso de que sea correcto, false
   * en caso de algun problema
   */
  public function start($directorio, $directorioPadre, $nombre, $cantidadArchivos, $campoDelimitador = "valor_minimo_diario_cosecha"){
    $resultado = false;
    $archivo = $this->getFicheroNuevo($directorio, $directorioPadre);
    if($archivo !=  null){
      if($this->esArchivoTransferenciaValido($archivo,null,null, $nombre, $cantidadArchivos)){
        $anioMes = $this->getAnioMes($archivo);
        if($anioMes != null){

          $archivosObjetivos = $this->getTodosLosArchivos($anioMes->mes
            , $anioMes->anio, $directorio, $nombre, $cantidadArchivos);
          if($archivosObjetivos != null && count($archivosObjetivos) == $cantidadArchivos){
            $this->archivos = $archivosObjetivos;
            $this->mes = $anioMes->mes;
            $this->anio = $anioMes->anio;
            $this->fecha = Carbon::create($this->anio, $this->mes, 1, 0, 0, 0);
            $resultado = $this
              ->identificarDelimitador(
                $cantidadArchivos == 1? $this->archivos[0]:$this->getArchivoByNro(1)
              ,$campoDelimitador);
          }
        }
      }
    }
    return $resultado;
  }



  /**
   * Devuelve todos los archivos del directorio, para el mes proporcionado
   * @param  String $mes Mes buscado (el cual es parte del nombre del fichero
   * en Transferencia)
   * @return array de archivos o null en caos de error
   */
  protected function getTodosLosArchivos($mes, $anio, $directorio, $nombreArchivo, $cantidadArchivos){

    $arregloArchivos = array();
    $todosLosArchivos = File::allFiles($directorio);
    for($i=0; $i < count($todosLosArchivos); ++$i){

      $archivo = $todosLosArchivos[$i];
      $encontrado = array_first($arregloArchivos
        , function ($value, $key) use($archivo) {

        return File::name($value) == File::name($archivo);
      }, false);

      if(!$encontrado && $this->esArchivoTransferenciaValido($archivo, $mes
        , $anio, $nombreArchivo, $cantidadArchivos)){
        array_push($arregloArchivos, $archivo);
      }
    }

    return $arregloArchivos;
  }


  /**
   * Verifica si el archivo proporcionado es un archivo de transferencia
   * valido
   * @param  File $archivo
   * @param $mes mes buscado, default null
   * @param $anio año buscado, default null
   * @return boolean
   */
  protected function esArchivoTransferenciaValido($archivo, $mes = null
    , $anio = null, $nombreInforme, $cantidadArchivos){
    $esValido = true;
    try{
      $nombre = File::name($archivo);
      $arregloNombre = explode("_", $nombre);
      if($arregloNombre[0] != "BOSQUESDELMAUCO"){
        $esValido = false;
      }
      if($arregloNombre[1] != $nombreInforme){
        $esValido = false;
      }
      $diferencia = 0;
      if($cantidadArchivos  == 1){
        $diferencia = 1;
      }
      if($cantidadArchivos != 1){
        $nroArchivo = (int) $arregloNombre[2];

        //los archivos pueden ir solo desde 1 hasta la cantidad
        //máxima
        if($nroArchivo > $cantidadArchivos && $nroArchivo <1){
          $esValido = false;
        }
      }
      //año debe ser entero
      if(!is_int((int)$arregloNombre[3 - $diferencia])){
        $esValido = false;
      }

      //mes debe ser entero
      if(!is_int((int)$arregloNombre[4 - $diferencia])){
        $esValido = false;
      }

      //si se verifica el año y mes, si estos no corresponden
      //entonces los archivos son invalidos
      if($mes != null && $mes != $arregloNombre[4 - $diferencia]){

        $esValido = false;
      }

      if($anio != null && $anio != $arregloNombre[3 - $diferencia]){
        $esValido = false;
      }

    }catch(OutOfBoundsException $ex){
      $esValido = false;
    }
    return $esValido;
  }


  /**
   * Devuelve el mes  y año relacionado en base a un archivo
   * según el estandar de archivos para liquidación
   * @param  File $archivo posible archivo de transferencia
   * @return Object del mes y annio o null en caso de error
   */
  protected function getAnioMes($archivo){
    $objeto = null;
    try{
      $nombre = File::name($archivo);
      //BOSQUESDELMAUCO_TRANSINFPROD_NROARCHIVO_AÑO_MES
      $arregloNombre = explode("_", $nombre);
      $diferencia = 0;
      if(count($arregloNombre) == 4){
        $diferencia = 1;
      }
      $objeto = new \stdClass();
      $mes = (int)$arregloNombre[4 -$diferencia];
      $anio = (int)$arregloNombre[3 - $diferencia];
      $objeto->mes = $mes;
      $objeto->anio = $anio;
    }catch(OutOfBoundsException $ex){
      $objeto = null;
    }
    return $objeto;
  }


  /**
   * Define cual de los delimitadores de ficheros posibles es el utilizado
   * @param  File $archivo archivo de transferencia
   * @param  String $campoParaValidar corresponde a campo que se busca
   * para asegurarse que el fichero fue procesado correctamente, por defecto
   * se usa el campo "valor_minimo_diario_cosecha"
   * @return boolean true en caso de exito al encontrar uno, false en caso
   * contrario
   */
  protected function identificarDelimitador($archivo, $campoParaValidar = "valor_minimo_diario_cosecha"){
    $encontrado = false;
    foreach(self::DELIMITADORES_POSIBLES as $del){
      VerificarSeparadorImport::$delimitador = $del;
      $ruta = $archivo->getPathName();
      $instanciaActual = $this;
      VerificarSeparadorImport::$encontrado=false;
      VerificarSeparadorImport::$campoParaValidar = $campoParaValidar;
      Excel::import(new VerificarSeparadorImport, $ruta, 'publico',\Maatwebsite\Excel\Excel::CSV);
      $encontrado = VerificarSeparadorImport::$encontrado;
      if($encontrado){

        $this->delimitador = $del;
        break;
      }
    }
    return $encontrado;
  }


  /**
   * Verifica el directorio, si no existe, lo crea
   * @param  String $directorio Ruta del nuevo/existente directorio
   */
  protected function verificarYCrearDirectorio($directorio){
    if(!File::exists($directorio)){
       File::makeDirectory($directorio);
    }
  }


  /**
   * Mueve un archivo al directorio del historial
   * @param  String $nombreArchivo Ruta completa del Archivo a mover
   * @param String $directorioOrigen Ruta del directorio de Origen
   * @param String $directorioHistorial Ruta específica del directorio de Historial
   * @return Resultado booleano al moverlo
   */
  public function mueveAHistorial($nombreArchivo, $directorioOrigen
    ,$directorioHistorial){

    $this->verificarYCrearDirectorio($directorioHistorial);

    $soloNombre = File::name($nombreArchivo);
    $soloExtension = File::extension($nombreArchivo);
    return File::move($directorioOrigen."/".$soloNombre.".".$soloExtension
        , $directorioHistorial."/"
          .$this->getNombreNuevoArchivo($soloNombre,$soloExtension));

  }



  /**
   * Mueve todos los archivos al historial
   * @param $directorioPadre: directorio contenedor del buscado
   * @param $directorioDirecto: directorio que incluye los archivos a mover
   * @param $directorioHistorial: directorio al cual mover ficheros
   */
  public function moverTodoAlHistorial($directorioPadre
      , $directorioDirecto, $directorioHistorial){
      $this->verificarYCrearDirectorio($directorioPadre);
      $this->verificarYCrearDirectorio($directorioDirecto);
      $listaArchivos = File::files($directorioDirecto);
      foreach($listaArchivos as $archivo){
        $this->mueveAHistorial($archivo, $directorioDirecto
            , $directorioHistorial);
      }
  }

  /**
   * Devuelve un nombre único para un archivo.
   * @param  String $nombreArchivo nombre del archivo (sin su ruta)
   * @param  String $extension    Extensión del archivo (sin .)
   * @return String nuevo nombre para el archivo
   */
  private  function getNombreNuevoArchivo($nombreArchivo, $extension){
    $carbon = Carbon::now();
    return $nombreArchivo."-".$carbon->format("YmdHis").".".$extension;
  }

  /**
   * De los archivos obtenidos en el inicio, busca el que corresponde
   * al número
   * @param  Integer $nro número buscado, desde 1 a la cantidad máxima
   * @return File archivo buscado, null en caso de no encontrarlo
   */
  protected function getArchivoByNro($nro){
    for($i=0; $i< count($this->archivos); ++$i){
      $arregloArchivo = explode("_",File::name($this->archivos[$i]));
      if($arregloArchivo[2] == $nro){
        return $this->archivos[$i];
      }
    }
    return null;
  }

  /**
   * Procesa los archivos a importar
   * @param  Integer $nro 1 para el que contiene movimientos y valores
   *                      mayor que 1 para kilos por dia
   *                      ( en el caso del prefijo "procesarArchivo", por defecto)
   * @return stdClass con la siguiente estructura
   *  {
   *    resultado: true en caso de exitoso, false en caso contrario
   *  , mensaje: String con el mensaje de error o null en caso de que
   *  la operacion fuera exitosa
   * }
   */
  public function procesarArchivo($nro, $prefijo = "procesarArchivo"){
    $resultado = new \stdClass();
    $resultado->resultado = true;
    $resultado->mensaje = null;
    try{
      $this->{$prefijo.$nro}();
    }catch(ErrorException $er){
      $resultado->resultado = false;
      $resultado->mensaje = $er;
    }catch(Exception $ex){
      $resultado->resultado = false;
      $resultado->mensaje = $ex;
    }
    return $resultado;
  }


}
