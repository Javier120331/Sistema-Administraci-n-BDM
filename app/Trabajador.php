<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
  protected $connection = "veritas";

  public function scopeCosechador(){
    return Cosechador::where('trabajador_id', "=", $this->id);
  }
  public function scopeGetByRut($query, $rut){
    return $query->where('rut', "=", $rut);
  }
  public function scopeTrabajadoresDisponibles($query){
    return $query->where("estado", "=", "1");
  }

  public function grupo(){
    return $this->belongsTo("App\Grupo", "grupo_id");
  }
  public function scopeByGrupo($query, $idGrupo){
    return $query->where("grupo_id", $idGrupo);
  }
}
