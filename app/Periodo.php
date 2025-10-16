<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TraductorFecha;
use Date;
class Periodo extends Model
{
    use TraductorFecha;
    protected $table = "periodos";
    protected $dates = [
        'created_at',
        'updated_at',
        'fecha_inicio',
        'fecha_termino',
    ];
    
    // Nota: Accessors getFechaTerminoAttribute y getFechaInicioAttribute
    // fueron removidos porque causaban conflicto con $dates (retornaban Date en lugar de Carbon)
    
    public function empleado(){
      return $this->belongsTo("App\Empleado", "empleado_id");
    }
    public function vacaciones(){
        return $this->hasMany("App\Vacacion","periodo_id");
    }
    public function scopeGetByNombre($query, $nombre){
      return $query->where("nombre", "=", $nombre);
    }
    public function scopeGetByFecha($query, $fecha){
      return $query->where('fecha_inicio','<=', $fecha)
        ->where('fecha_termino','>=', $fecha);
    }
    public function scopeGetEnRango($query, $fechaInicio, $fechaTermino){
      return $query->where(function($q) use($fechaInicio,$fechaTermino){
              $q->where("fecha_inicio", "<=", $fechaInicio)
                ->where("fecha_termino", ">=", $fechaInicio);
              })->orWhere(function($q) use($fechaInicio,$fechaTermino){
                   $q->where("fecha_inicio", "<=", $fechaTermino)
                   ->where("fecha_termino", ">=", $fechaTermino);
              });
    }
}
