<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TraductorFecha;
use Date;

class MovimientoAsistenciaExport extends Model
{
    use TraductorFecha;
    protected $table="movimientos_asistencias_exportaciones";
    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = [
      'created_at',
      'updated_at',
      'fecha_documento',
      'fecha_inicio_movimientos',
      'fecha_termino_movimientos',
      'fecha_generacion'
    ];

     public function scopeGetByRutEmpleado($query,$rutEmpleado){
        return $query->where("rut_empleado","=",$rutEmpleado);
      }

    public function getFechaDocumentoAttribute($fecha_documento)
    {
        return new Date($fecha_documento);
    }

    public function getFechaInicioMovimientosAttribute($fecha_inicio_movimientos)
    {
        return new Date($fecha_inicio_movimientos);
    }
    public function getFechaTerminoMovimientosAttribute($fecha_termino_movimientos)
    {
        return new Date($fecha_termino_movimientos);
    }
    public function getFechaGeneracionAttribute($fecha_generacion)
    {
        return new Date($fecha_generacion);
    }
}
