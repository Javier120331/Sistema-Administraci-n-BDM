<?php

namespace App\DAO;

use App\Produccion;
use Carbon\Carbon;
use DB;
class ProduccionService
{
  public static function rollBack($fechaInicio, $fechaTermino){
    $sql = "DELETE FROM producciones WHERE fecha BETWEEN '"
      .$fechaInicio->toDateString(). "' AND '".$fechaTermino->toDateString()."'";
    DB::statement($sql);

    $sql = "DELETE FROM movimientos_mensuales WHERE fecha BETWEEN '"
      .$fechaInicio->toDateString(). "' AND '".$fechaTermino->toDateString()."'";
    DB::statement($sql);
  }
  /**
   * Obtiene las producciones de un mes
   * @param  List<Cosechador> $cosechadores Cosechadores a considerar
   * @param  Carbon\Carbon $mes  Mes a considerar
   * @return List<Producciones> lista de producciones
   */
  public static function getProduccionesCosechadores($cosechadores, $mes){
    $inicio = $mes->copy()->subMonth();
    $inicio->day = 21;
    $fin = $mes->copy();
    $fin->day = 20;
    $idsCosechadores = $cosechadores->pluck("id");
    $sql = "SELECT p.cosechador_id as 'IdCosechador'"
          .  "   , tp.abreviacion as 'TipoProduccion'"
          .  "   , p.fecha as 'FechaProduccion'"
          .  "   , m.kilos"
          .  "   , ca.factor"
          .  "   , ca.id_calidad as 'CalidadVeritas'"
          .  "   , ca.id as 'IdCalidad'"
	          .  " FROM producciones p"
          .  " INNER JOIN tipos_producciones tp ON p.tipo_produccion_id=tp.id"
	          .  " INNER JOIN movimientos m ON m.produccion_id=p.id"
	          .  " INNER JOIN calidades ca ON m.calidad_id=ca.id "
	          .  " WHERE p.fecha >= '".$inicio->toDateString()."'"
            .  "    AND p.fecha<='".$fin->toDateString()."'"
            . "    AND p.cosechador_id IN (".implode(",",$idsCosechadores->toArray()).")"
            . "  ORDER BY p.fecha ASC";
   return DB::select($sql);
  }

  public static function getProduccionesByCosechador($idsCosechadores = null){
    $sql =  "SELECT p.fecha, tp.abreviacion, SUM(m.kilos) as kilosTotales"
	         . ", (SUM(m.kilos)* c.factor) as valorTotal, c.id_calidad as idCalidad"
           . " FROM producciones p"
           . " INNER JOIN tipos_producciones tp ON tp.id = p.tipo_produccion_id"
           . " INNER JOIN movimientos m ON p.id=m.produccion_id"
           . " INNER JOIN calidades c ON c.id= m.calidad_id";

    if($idsCosechadores != null){
      $sql.= " WHERE cosechador_id IN(".join(',',$idsCosechadores).")";
    }

    $sql.= " GROUP BY c.id, p.id, p.cosechador_id"
         . " ORDER BY p.fecha ASC";

    return DB::select($sql);
  }

}
