<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Dato;

class Dato extends Model
{
    //Uno a muchos (inversa)
    //Un dato solo puede pertencer a una remisión
    public function remision(){
        return $this->belongsTo(Dato::class);
    }
}
