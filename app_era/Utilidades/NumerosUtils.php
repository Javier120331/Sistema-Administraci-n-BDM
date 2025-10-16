<?php

  namespace App\Utilidades;


  class NumerosUtils{

    /**
     * Formatea el valor  como moneda chilena
     * @param   Integer $valor valor numerico a formatear
     * @return String  moneda formateada, con punto separador de miles
     */
    public static function getMoneda($valor){
      return number_format($valor, 0,null,'.');
    }

    /**
     * Limpia un valor tipo moneda chilena de su formato
     * @param  String $moneda moneda formateada, con punto separador de miles
     * @return Integervalor numerico original
     */
    public static function unformatMoneda($moneda){
      return str_replace('.','',$moneda);
    }
  }
