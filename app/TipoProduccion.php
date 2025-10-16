<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoProduccion extends Model
{
  protected $table = "tipos_producciones";

  public function scopeGetByAbreviacion($query, $abrev){
    return $query->where("abreviacion", "=", $abrev);
  }

  public function producciones(){
    return $this->hasMany("App\Produccion", "tipo_produccion_id");
  }
}
