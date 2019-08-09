<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Repayment;
use App\Registro;

class Entrada extends Model
{
    protected $fillable = [
        'id', 
        'folio',
        'editorial',
        'unidades', 
        'total',
        'total_pagos'
    ];

    //Uno a muchos
    //Una entrada puede tener muchos registros
    public function registros(){
        return $this->hasMany(Registro::class);
    }

    //Uno a muchos
    //Una entrada puede tener muchos pagos
    public function repayments(){
        return $this->hasMany(Repayment::class);
    }

}
