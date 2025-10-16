<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{

  public function scopeGetByNombre($query, $nombre){
    return $query->where("nombre","=",$nombre);
  }

}
