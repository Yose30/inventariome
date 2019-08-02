<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Devolucione;
use App\Departure;
use App\Register;
use App\Registro;
use App\Vendido;
use App\Dato;

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

    //Uno a muchos
    //Un libro puede pertenecer a muchas vendidos 
    public function vendidos(){
        return $this->hasMany(Vendido::class);
    }

    //Uno a muchos
    //Un libro puede pertenecer a muchos registros de nota
    public function registers(){
        return $this->hasMany(Register::class);
    }

    //Uno a muhcos
    //Un libro puede tener muchas salidas
    public function departures(){
        return $this->hasMany(Departure::class);
    }
}
