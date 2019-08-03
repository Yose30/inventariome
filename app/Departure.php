<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Promotion;
use App\Devuelta;
use App\Libro;

class Departure extends Model
{
    protected $fillable = [
        'id', 
        'promotion_id', 
        'libro_id', 
        'unidades', 
        'unidades_pendientes'
    ];
    
    //Uno a muchos (Inversa)
    //Una salida solo puede pertenecer a una promoción
    public function promotion(){
        return $this->belongsTo(Promotion::class);
    }

    //Uno a muchos (Inversa)
    //Una salida solo puede tener un libro
    public function libro(){
        return $this->belongsTo(Libro::class);
    }

    //Uno a uno
    //Una salida solo puede tener una devolucion
    public function devuelta(){
        return $this->hasOne(Devuelta::class);
    }

}
