<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    protected $table="licencias";
    protected $dates = [
      'created_at',
      'updated_at',
      'fecha_inicio',
      'fecha_termino'
    ];
    public function scopeGetByEstadoAndCausaTipo($query, $estado,$causa,$tipo){
      $builder = $query->where("estado", "=", $estado);
      if($causa !=null){
        $builder =  $builder->where("causa_licencia_id","=", $causa);
      }
      if($tipo !=null){
        $builder =  $builder->where("tipo","=", $tipo);
      }
      return $builder;
    }
    public function scopeGetByFechas($query,$fechaInicio,$fechaTermino){
      return $query->where("fecha_inicio","=", $fechaInicio)
                   ->where("fecha_termino","=", $fechaTermino);
    }
    public function movimientosAsistencia(){
        return $this->hasMany("App\MovimientoAsistencia","licencia_id");
    }

    public function causa(){
      return $this->belongsTo("App\CausaLicencia", "causa_licencia_id");
    }
    public function empleado(){
      return $this->belongsTo("App\Empleado", "empleado_id");
    }
}
