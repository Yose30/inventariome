<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes; //línea necesaria
use Illuminate\Database\Eloquent\Model;
use App\Entrada;
use App\Libro;

class Registro extends Model
{
    use SoftDeletes; //Implementamos 

    protected $dates = ['deleted_at']; //Registramos la nueva columna

    protected $fillable = [
        'id', 
        'entrada_id', 
        'libro_id',
        'costo_unitario', 
        'unidades', 
        'total'
    ];

    //Uno a muchos (inversa)
    //Un registro solo puede pertencer a una entrada
    public function entrada(){
        return $this->belongsTo(Entrada::class);
    }

    //Uno a muchos (Inversa)
    //Un registro solo puede tener un libro
    public function libro(){
        return $this->belongsTo(Libro::class);
    }
    
}
