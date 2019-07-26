<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Dato;
use App\Remisione;
use App\Libro;
use App\Pago;

class Vendido extends Model
{
    protected $fillable = [
        'id', 
        'remision_id', 
        'dato_id', 
        'libro_id',
        'unidades', 
        'unidades_resta',
        'total', 
        'total_resta',
        'unidades_base',
        'total_base'
    ];

    //Uno a muchos (inversa)
    //Un vendido solo puede pertencer a una remisión
    public function remision(){
        return $this->belongsTo(Remisione::class);
    }

    //Uno a uno
    //Un vendido solo puede pertenecer a un dato
    public function dato(){
        return $this->belongsTo(Dato::class);
    }

    //Uno a muchos (Inversa)
    //Un vendido solo puede tener un libro
    public function libro(){
        return $this->belongsTo(Libro::class);
    }

    //Uno a muchos
    //Un vendido puede tener muchos pagos
    public function pagos(){
        return $this->hasMany(Pago::class);
    }
}
