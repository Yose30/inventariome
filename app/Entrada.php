<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Registro;

class Entrada extends Model
{
    protected $fillable = [
        'id', 
        'unidades', 
        'total'
    ];

    //Uno a muchos
    //UNa entrada puede tener muchos registros
    public function registros(){
        return $this->hasMany(Registro::class);
    }
}
