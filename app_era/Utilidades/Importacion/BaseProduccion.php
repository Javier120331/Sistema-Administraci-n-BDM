<?php

  namespace App\Utilidades\Importacion;

  use App\Utilidades\ImportacionProduccionUtils;
  use DB;

  class BaseProduccion{

     private $importacionUtils;

     public function __construct(){
       $this->importacionUtils = new ImportacionProduccionUtils();
     }


     public function iniciar(){
       return $this->importacionUtils->iniciar();
     }

     public function moverTodoAHistorial(){
       return $this->importacionUtils->moverTodoAHistorial();
     }
     /**
      * Procesa el contenido de todos los archivos y los ingresa a la base de datos
      * @return stdClass  con la estructura{
      * {  resultado: true or false
      * , mensaje: String o null en caso de exito
      * }
      *
      */
     public function procesarArchivos(){
       ini_set('max_execution_time', 600);
       try{
         $resultado = new \stdClass();
         $resultado->resultado = true;
         $resultado->mensaje = null;
         DB::beginTransaction();
         for($i=1; $i <= $this->importacionUtils->getCantidadArchivos();++$i){
           $resultado = $this->importacionUtils->procesarArchivo($i);
           if(!$resultado->resultado){
             break;
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
