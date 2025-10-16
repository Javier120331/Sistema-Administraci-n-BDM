<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bono extends Model
{


 public function scopeGetByTipoAndFecha($query, $idTipo,$fecha){
   return $query->where("tipo_bono_id","=",$idTipo)
                ->whereDate("fecha",$fecha);
 }

  public function empleado(){
    return $this->belongsTo("App\Empleado", "empleado_id");
  }

  public function tipoBono(){
    return $this->belongsTo("App\TipoBono", "tipo_bono_id");
  }
}
