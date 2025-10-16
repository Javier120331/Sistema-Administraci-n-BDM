<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
  protected $table="movimientos";

  public function scopeGetByCalidad($query, $idCalidad){
    return $query->where("calidad_id", "=", $idCalidad);
  }

  public function produccion(){
    return $this->belongsTo("App\Produccion", "produccion_id");
  }

  public function calidad(){
    return $this->belongsTo("App\Calidad", "calidad_id");
  }
}
