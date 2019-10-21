<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Remisione;
use App\Libro;

class Donacione extends Model
{
    protected $fillable = [
        'id', 
        'remisione_id',
        'libro_id',
        'unidades',
    ];

    //Uno a muchos (inversa)
    //Una donacion solo puede pertencer a una remisión
    public function remision(){
        return $this->belongsTo(Remisione::class);
    }

    //Uno a muchos (Inversa)
    //Una donacion solo puede tener un libro
    public function libro(){
        return $this->belongsTo(Libro::class);
    }
}
