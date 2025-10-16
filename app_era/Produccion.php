<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{
  protected $table = "producciones";

  protected $dates = [
      'created_at',
      'updated_at',
      'fecha'
  ];
  public function scopeGetByFecha($query, $fecha){
    return $query->where("fecha", "=", $fecha);
  }

  public function scopeGetByMes($query, $mes){

    $inicio = $mes->copy()->subMonth();
    $inicio->day = 21;
    $fin = $mes->copy();
    $fin->day = 20;

    return $query->whereBetween("fecha",[$inicio, $fin]);
  }

  public function scopeGetByFechaAndIdCosechador($query, $fecha, $idCosechador){
    return $query->where("fecha", "=", $fecha)
                 ->where("cosechador_id", "=", $idCosechador);
  }
  public function cosechador(){
    return $this->belongsTo("App\Cosechador", "cosechador_id");
  }

  public function movimientos(){
    return $this->hasMany("App\Movimiento");
  }

  public function tipoProduccion(){
    return $this->belongsTo("App\TipoProduccion", "tipo_produccion_id");
  }
}
