<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CausaFiniquito extends Model
{
    protected $table="causas_finiquitos";

    public function finiquitos(){
        return $this->hasMany("App\Finiquito", "causa_finiquito_id");
    }
}
