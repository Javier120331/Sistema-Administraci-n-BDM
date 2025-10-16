<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cosechador extends Model
{
  protected $table="cosechadores";


  public function producciones(){
    return $this->hasMany("App\Produccion", "cosechador_id");
  }

  public function movimientosMensuales(){
    return $this->hasMany("App\MovimientoMensual", "cosechador_id");
  }

  public function scopeGetByRut($query, $rut){
    return $query->where("rut", "=", $rut);
  }

  public function scopeTrabajador(){
    return Trabajador::find($this->trabajador_id);
  }
}
