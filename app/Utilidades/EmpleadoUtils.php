<?php

   namespace App\Utilidades;
   use App\Empleado;
   use App\Comuna;
   use App\Utilidades\Constantes;
   class EmpleadoUtils{

     public static function generarEmpleadoHistorico($rut){
       $empleado = new Empleado();
       $empleado->rut = $rut;
       //Lo asignamos en la comuna origen de BMAUCO
       $empleado->comuna_id = Comuna::getByNombre("QUINTERO")->first()->id;
       $empleado->area_id = null;
       $empleado->nombre = "HISTORICO";
       $empleado->estado = Constantes::EMPLEADO_DESHABILITADO;
       $empleado->save();
       return $empleado;
     }
   }
