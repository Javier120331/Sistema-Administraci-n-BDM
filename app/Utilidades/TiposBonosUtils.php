<?php

  namespace App\Utilidades;


  class TiposBonosUtils{
      //Tipos de bonos existentes, y de los cuales se deben
      //procesar. Incluyen como llave la clave del Excel de finiquitos
      //y como valor el nombre de fantasía que debe tener
      const TIPOS_BONOS=[
         "bono_de_cargo" => "Bono de Cargo",
         "bono_de_asistencia"=> "Bono de Asistencia",
         "bono_de_desempeno_cargo" => "Bono de Desempeño de Cargo",
         "bono_desempeno_area" => "Bono de Desempeño por Área",
         "bono_turno_noche" => "Bono por Turno de Noche",
         "bono_incentivo" => "Bono por Incentivo",
         "bono_premio" => "Bono Premio",
         "bono_prod_calculado" => "Bono de Producción Calculado",
         "haberes_pendientes"=> "Haberes Pendientes",
         "comision" => "Comisión",
         "semana_corrida" => "Semana Corrida",
         "viatico" => "Viático",
         "movilizacion_maternal" => "Movilización Maternal",
         "t_movilizacion" => "Transporte y Movilización",
         "gratif_mensual" => "Gratificación Mensual"
      ];

      public static function getNombreByLlave($llave){
         return TiposBonosUtils::TIPOS_BONOS[$llave];
      }
      

  }
