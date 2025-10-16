<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TraductorFecha;
use Date;
class Finiquito extends Model
{
    use TraductorFecha;
    protected $table="finiquitos";
    protected $dates = [
        'created_at',
        'updated_at',
        'fecha_documento',
        'fecha_inicio_contrato',
        'fecha_finiquito'
    ];
    public function getFechaDocumentoAttribute($fecha_documento){
      return new Date($fecha_documento);
    }
    public function getFechaInicioContratoAttribute($fecha_inicio_contrato){
      return new Date($fecha_inicio_contrato);
    }
    public function getFechaFiniquitoAttribute($fecha_finiquito){
      return new Date($fecha_finiquito);
    }
    public function causaFiniquito(){
      return $this->belongsTo("App\CausaFiniquito", "causa_finiquito_id");
    }

    public function empleado(){
      return $this->belongsTo("App\Empleado", "empleado_id");
    }
}
