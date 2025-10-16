<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Feriado extends Model
{
  protected $primaryKey="fecha";
  protected $dates = [
    'created_at',
    'updated_at',
    'fecha'
  ];
  public $incrementing = false;

  public function scopeGetByAnio($query, $anio){
      $fechaInicio = new Carbon("first day of january ".$anio);
      $fechaFin = new Carbon("last day of december ".$anio);
      return $query->whereBetween("fecha", [$fechaInicio,$fechaFin]);
  }
}
