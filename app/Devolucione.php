<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Remisione;
use App\Dato;

class Devolucione extends Model
{
    protected $fillable = [
        'id', 
        'remision_id', 
        'dato_id', 
        'clave_libro', 
        'titulo', 
        'unidades', 
        'costo_unitario', 
        'total', 
        'total_resta',
        'unidades_resta'
    ];

    //Uno a muchos (inversa)
    //Una devolucion solo puede pertencer a una remisión
    public function remision(){
        return $this->belongsTo(Remisione::class);
    }

    //Uno a uno
    //Una devolcuion solo puede pertenecer a un dato
    public function dato(){
        return $this->belongsTo(Dato::class);
    }
}
