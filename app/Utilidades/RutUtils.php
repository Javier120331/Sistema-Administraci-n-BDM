<?php

  namespace App\Utilidades;


  class RutUtils{

    /**
     * Sanitiza un rut desde deFontana
     *
     * Limpia el rut desde deFontana (el cual contiene 0 inicial y separadores
     * '.', y lo convierte en el rut que utiliza el sistema de pesaje
     *
     * @param String rut Rut en el formato proporcionado por deFontana
     * @return {rut limpio}
     */
    public static function sanitizar($rut)
    {
      $rut = ltrim($rut,'0');
      $rut = str_replace('.','', $rut);
      return $rut;
    }

    public static function getRutDeFontana($rut){

      $arregloRut = explode('-', $rut);
      $rutFinal = number_format($arregloRut[0],0,'','.');
      $rutFinal = self::agregarZerosFaltantesIzquierda($rutFinal,10);
      return $rutFinal."-".$arregloRut[1];
    }
    /**
     * En base a un número, agrega la cantidad determinada de Zeros a la
     * izquierda, hasta completar el largo determinado
     * @param  Integer $numero
     * @param  Integer $largo largo esperado del String
     * @return String  número en formato String con Zeros a la izquierda.
     */
    private static function agregarZerosFaltantesIzquierda($numero, $largo){

      $caracteresFaltantes = $largo - strlen($numero);
      return self::agregarZerosIzquierda($numero, $caracteresFaltantes);
    }
    /**
     * En base a un número, agrega una cantidad determinada de Zeros a la
     * izquierda.
     * @param  Integer $numero
     * @param  Integer $cantidad Cantidad de Zeros a agregar a la izquierda.
     * @return String  número en formato String con Zeros a la izquierda.
     */
    private static function agregarZerosIzquierda($numero, $cantidad){

      for($i=0; $i<$cantidad; ++$i){
        $numero = "0".$numero;
      }
      return $numero;
    }
  }
