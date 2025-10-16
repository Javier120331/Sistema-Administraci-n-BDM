<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VacacionProgresiva extends Model
{
    protected $table= "vacaciones_progresivas";
    protected $dates = [
      'created_at',
      'updated_at',
      'fecha'
    ];
    public function empleado(){
      return $this->belongsTo("App\Empleado", "empleado_id");
    }
    public function scopeGetByFechas($query,$fechaInicio,$fechaTermino){
      return $query->whereBetween("fecha", [$fechaInicio,$fechaTermino]);
    }
}
