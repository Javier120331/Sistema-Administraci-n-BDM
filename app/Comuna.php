<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comuna extends Model
{
  public function empleados(){
    return $this->hasMany("App\Empleado", "comuna_id");
  }

  public function scopeGetByNombre($query, $nombre){
    return $query->where("nombre","=",$nombre);
  }
}
