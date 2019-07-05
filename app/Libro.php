<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Dato;
use App\Devolucione;
use App\Registro;

class Libro extends Model
{
    protected $fillable = [
        'id', 
        'ISBN',  
        'titulo', 
        'autor', 
        'editorial', 
        'edicion', 
        'piezas'
    ];

    //Uno a muchos
    //Un libro puede pertenecer a muchos registros de salida
    public function datos(){
        return $this->hasMany(Dato::class);
    }

    //Uno a muchos
    //Un libro puede pertenecer a muchas devoluciones 
    public function devoluciones(){
        return $this->hasMany(Devolucione::class);
    }

    //Uno a muchos
    //Un libro puede pertenecer a muchos registros de entrada
    public function registros(){
        return $this->hasMany(Registro::class);
    }
}
