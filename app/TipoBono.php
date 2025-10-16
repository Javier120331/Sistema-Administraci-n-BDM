<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoBono extends Model
{
  protected $table="tipos_bonos";

  public function scopeGetByNombre($query, $nombre){
    return $query->where("nombre","=",$nombre);
  }
  public function bonos(){
    return $this->hasMany("App\Bono", "tipo_bono_id");
  }
}
