<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calidad extends Model
{
  protected $table="calidades";

  public function scopeGetByCalidadVeritasId($query, $id){
    return $query->where('id_calidad', "=", $id);
  }
  /**
   * Devuelve la calidad relacionada en Veritas
   * @return App\CalidadVeritas
   */
  public function scopeCalidadVeritas(){
    return CalidadVeritas::find($this->id_calidad);
  }
}
