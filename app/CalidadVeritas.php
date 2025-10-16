<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalidadVeritas extends Model
{
  protected $connection = "veritas";
  protected $table = "calidads";

  public function scopeGetLikeNombre($query, $nombre){
    return $query->where("nombre", "like", $nombre);
  }
}
