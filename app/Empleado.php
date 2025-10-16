<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utilidades\Constantes;
use App\Traits\TraductorFecha;
use Date;
class Empleado extends Model
{
  use TraductorFecha;
  protected $dates = [
      'created_at',
      'updated_at',
      'fecha_inicio_contrato',
      'fecha_termino_contrato'
  ];

  // Nota: Los accessors getFechaInicioContratoAttribute y getFechaTerminoContratoAttribute
  // fueron removidos porque causaban conflicto con $dates (retornaban Date en lugar de Carbon)
  
  public function scopeGetByRut($query, $rut){
    return $query->where("rut", "=", $rut);
  }

  public function scopeGetByEstado($query, $estado){
    return $query->where("estado","=", $estado);
  }
  public function scopeDisponibles($query){
    return $query->where("estado","=", Constantes::EMPLEADO_HABILITADO);
  }

  public function area(){
    return $this->belongsTo("App\Area", "area_id");
  }

  public function comuna(){
    return $this->belongsTo("App\Comuna", "comuna_id");
  }

  public function licencias(){
    return $this->hasMany("App\Licencia", "empleado_id");
  }
  
  public function finiquitos(){
    return $this->hasMany("App\Finiquito", "empleado_id");
  }

  public function vacacionesProgresivas(){
    return $this->hasMany("App\VacacionProgresiva", "empleado_id");
  }

  public function periodos(){
    return $this->hasMany("App\Periodo", "empleado_id");
  }
  public function movimientosAsistencias(){
    return $this->hasMany("App\MovimientoAsistencia", "empleado_id");
  }

  public function sueldosBase(){
    return $this->hasMany("App\SueldoBase", "empleado_id");
  }
  public function cargosEmpleado(){
    return $this->hasMany("App\CargoEmpleado", "empleado_id");
  }

  public function getCargoActual(){
    return $this->cargosEmpleado()->where("estado","=",1);
  }

  public function bonos(){
    return $this->hasMany("App\Bono", "empleado_id");
  }
}
