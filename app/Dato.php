<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Remisione;
use App\Devolucione;
use App\Libro;

class Dato extends Model
{
    protected $fillable = [
        'id', 
        'remision_id', 
        'libro_id',
        'unidades', 
        'total',
        'estado'
    ];

    //Uno a muchos (inversa)
    //Un dato solo puede pertencer a una remisión
    public function remision(){
        return $this->belongsTo(Remisione::class);
    }

    //Uno a uno
    //Un dato solo puede pertenecer a una devolucion
    public function devolucione(){
        return $this->hasOne(Devolucione::class);
    }

    //Uno a muchos (Inversa)
    //Un dato solo puede tener un libro
    public function libro(){
        return $this->belongsTo(Libro::class);
    }
}
