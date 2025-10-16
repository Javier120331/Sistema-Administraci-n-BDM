<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TraductorFecha;
use Date;
class Vacacion extends Model
{
    use TraductorFecha;
    protected $table="vacaciones";
    protected $dates = [
        'created_at',
        'updated_at',
        'fecha_inicio',
        'fecha_termino',
        'fecha_registro',
    ];

    // Nota: Accessors getFechaTerminoAttribute, getFechaInicioAttribute y getFechaRegistroAttribute
    // fueron removidos porque causaban conflicto con $dates (retornaban Date en lugar de Carbon)

    public function scopeGetByFechas($query,$fechaInicio, $fechaTermino){
      return $query->whereBetween("fecha_inicio", [$fechaInicio,$fechaTermino]);
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

    /**
     * Obtiene las vacaciones que comienzan en el rango de fechas determinado
     * utilizado en la construcción del pdf de vacaciones y calculo
     * de días ya autorizados
     */
    public function scopeGetEnRangoInicio($query, $fechaInicio, $fechaTermino){
      return $query->where("fecha_inicio", ">=", $fechaInicio)
                   ->where("fecha_inicio", "<=", $fechaTermino);
    }

    public function movimientosAsistencia(){
        return $this->hasMany("App\MovimientoAsistencia","vacaciones_id");
    }

    public function periodo(){
      return $this->belongsTo("App\Periodo", "periodo_id");
    }
}
