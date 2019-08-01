<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Registro;
use App\Pago;

class Entrada extends Model
{
    protected $fillable = [
        'id', 
        'folio',
        'editorial',
        'unidades', 
        'total'
    ];

    //Uno a muchos
    //Una entrada puede tener muchos registros
    public function registros(){
        return $this->hasMany(Registro::class);
    }

    //Uno a muchos
    //Una entrada puede tener muchos pagos
    public function pagos(){
        return $this->hasMany(Pago::class);
    }
}
