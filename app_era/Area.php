<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = "areas";
    public function empleados(){
          return $this->hasMany("App\Empleado", "area_id");
    }

    public function scopeGetByNombre($query, $nombre){
      return $query->where("nombre","=",$nombre);
    }

}
