<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoAsistencia extends Model
{
      protected $table="movimientos_asistencias";
      
      protected $dates = [
          'created_at',
          'updated_at',
          'fecha',
      ];

      public function scopeGetByEmpleadoAndFechaAndTipo($query,$idEmpleado, $fecha, $tipo){
        return $query->where("empleado_id","=",$idEmpleado)
                     ->whereDate("fecha",$fecha)
                     ->where("tipo_asistencia","=",$tipo);
      }

      public function empleado(){
          return $this->belongsTo("App\Empleado", "empleado_id");
      }
}
