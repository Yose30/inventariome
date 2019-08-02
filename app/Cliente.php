<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Remisione;
use App\Adeudo;

class Cliente extends Model
{
    protected $fillable = [
        'id', 'name', 'contacto', 'email', 'telefono', 'direccion', 'descuento', 'condiciones_pago'
    ];

    //Uno a muchos
    //Un cliente puede tener muchas remisiones
    public function remisiones(){
        return $this->hasMany(Remisione::class);
    }

    //Uno a muchos
    //Un cliente puede tener muchos adeudos
    public function adeudos(){
        return $this->hasMany(Adeudo::class);
    }
}
