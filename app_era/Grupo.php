<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
  protected $table = "grupos";
  protected $connection = "veritas";

  public function scopeGruposDisponibles($query){
    return $query->where('estado','=',1)
                 ->where("nombre", "!=", "NO APLICA")
                 ->where("nombre", "!=", "NO_DEFINIDO");
  }

  public function trabajadores(){
    return $this->hasMany("App\Trabajador");
  }
}
