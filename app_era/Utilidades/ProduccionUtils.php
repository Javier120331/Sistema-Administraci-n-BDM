<?php
namespace App\Utilidades;

class ProduccionUtils{

    /**
     * Filtra las producciones y devuelve solo las del cosechador especificado
     * @param  Array $producciones Producciones desde la base de datos
     * @param  Cosechador $cosechador
     * @return Array de producciones del cosechador
     */
    public static function getProduccionesByCosechador ($producciones, $cosechador){
      $produccionesCosechador = array_where($producciones, function($valor,$llave)
        use ($cosechador){
          return $valor->IdCosechador == $cosechador->id;
      });
      return $produccionesCosechador;
    }

    public static function getProduccionesByFechaAndCalidad($producciones, $fecha, $calidad){
      $produccionesFecha = array_where($producciones, function($valor,$llave)
        use ($fecha, $calidad){
          return $valor->FechaProduccion == $fecha && $valor->IdCalidad == $calidad->id;
      });
      return $produccionesFecha;
    }

    /**
     * Filtra las producciones y devuelve solo las de la calidad especificada
     * @param  Array $producciones Producciones desde la base de datos
     * @param  Calidad $calidad
     * @return Array de producciones de la calidad
     */
    public static function getProduccionesByCalidad ($producciones, $calidad){
      $produccionesCosechador = array_where($producciones, function( $valor,$llave)
        use ($calidad){
          return $valor->IdCalidad == $calidad->id;
      });
      return $produccionesCosechador;
    }

}
