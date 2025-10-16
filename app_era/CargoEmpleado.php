<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoEmpleado extends Model
{
  protected $table="cargos_empleados";

  public function scopeGetByCargoAndFecha($query, $idCargo, $fecha){
    return $query->where("cargo_id", "=", $idCargo)
                 ->whereDate("fecha_inicio_cargo", $fecha);
  }

  /**
   * Busca si existe un cargo más nuevo que el buscado en la fecha descrita
   * @param  QueryBuilder $query
   * @param  Carbon $fecha
   * @return QueryBuilder
   */
  public function scopeGetMasNuevoQue($query, $fecha){
    return $query->whereDate("fecha_inicio_cargo", ">", $fecha);
  }

  /**
   * Obtiene los cargos que son deshabilitables, es decir
   * que no tienen el fin de contrato definido y son menores a la fecha
   * @param  QueryBuilder $query
   * @param  Carbon $fecha
   * @return QueryBuilder
   */
  public function scopeGetDeshabilitables($query, $fecha){

    return $query->whereNull("fecha_termino_cargo")
                   ->whereDate("fecha_inicio_cargo", "<", $fecha);
  }

  public function empleado(){
    return $this->belongsTo("App\Empleado", "empleado_id");
  }
  public function cargo(){
    return $this->belongsTo("App\Cargo", "cargo_id");
  }
}
