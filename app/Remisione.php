<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cliente;
use App\Dato;

class Remisione extends Model
{
    //Uno a muchos (Inversa)
    //Una remision solo puede pertenecer a un cliente
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    //Uno a muchos
    //UNa remision puede tener muchos datos
    public function datos(){
        return $this->hasMany(Dato::class);
    }
}
