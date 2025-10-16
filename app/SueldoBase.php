<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilidades\Constantes;
class SueldoBase extends Model
{
  protected $table="sueldos_bases";
  public function empleado(){
    return $this->belongsTo("App\Empleado", "empleado_id");
  }


  public function scopeGetSueldosActivos($query,$fecha){
    return $query->where("estado", "=",Constantes::SUELDO_ACTUAL)
                 ->whereDate("fecha_asignacion", "<", $fecha);
  }

  public function scopeGetByValorAndFecha($query, $valor,$fecha){
    return $query->where("valor","=",$valor)
                 ->whereDate("fecha_asignacion", $fecha);
  }
  public function scopeGetMasNuevoQue($query,$fecha){
     return $query->whereDate("fecha_asignacion", ">", $fecha);
  }
}
