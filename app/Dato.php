<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Remisione;
use App\Devolucione;

class Dato extends Model
{
    protected $fillable = [
        'id', 'remision_id', 'isbn_libro', 'titulo', 'unidades', 'costo_unitario', 'total'
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
}
