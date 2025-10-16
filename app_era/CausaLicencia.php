<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CausaLicencia extends Model
{
    protected $table="causas_licencias";
    public function scopeGetByNombre($query, $nombre){
      return $query->where("nombre", "=", $nombre);
    }
}
