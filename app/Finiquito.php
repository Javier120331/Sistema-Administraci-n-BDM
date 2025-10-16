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
    
    // Nota: Accessors getFechaDocumentoAttribute, getFechaInicioContratoAttribute y getFechaFiniquitoAttribute
    // fueron removidos porque causaban conflicto con $dates (retornaban Date en lugar de Carbon)
    
    public function causaFiniquito(){
      return $this->belongsTo("App\CausaFiniquito", "causa_finiquito_id");
    }

    public function empleado(){
      return $this->belongsTo("App\Empleado", "empleado_id");
    }
}
