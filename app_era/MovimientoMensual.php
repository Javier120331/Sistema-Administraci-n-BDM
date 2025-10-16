<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoMensual extends Model
{
  protected $table="movimientos_mensuales";

  public function scopeGetByTipoProduccion($query, $tipo, $mes){
    $inicio = $mes->copy()->subMonth();
    $inicio->day = 21;
    $fin = $mes->copy();
    $fin->day = 20;
    return $query->where("tipo_no_produccion", "=", $tipo)
            ->whereBetween("fecha",[$inicio, $fin]);
  }

  public function scopeGetPrecioUnitario($query, $nombreCalidad ,$mes){

    $nombreCalidad = str_replace(" ","_",strtoupper($nombreCalidad));
    $inicio = $mes->copy()->subMonth();
    $inicio->day = 21;
    $fin = $mes->copy();
    $fin->day = 20;
    return $query->where("tipo_no_produccion","="
      ,'PRECIO_UNITARIO_'.$nombreCalidad)
     ->whereBetween("fecha",[$inicio, $fin]);;
  }
  
  public function cosechador(){
    return $this->belongsTo("App\Cosechador", "cosechador_id");
  }
}
