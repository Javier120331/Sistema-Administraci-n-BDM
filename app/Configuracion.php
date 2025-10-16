<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
  protected $table = "configuraciones";
  public function scopeGetByNombre($query, $nombre){
    return $query->where("nombre", "=", $nombre);
  }
}
