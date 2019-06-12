<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Remisione;

class Cliente extends Model
{
    protected $fillable = [
        'name', 'last_name', 'email', 'telefono', 'direccion', 'descuento', 'condiciones_pago'
    ];

    //Uno a muchos
    //Un cliente puede tener muchas remisiones
    public function remisiones(){
        return $this->hasMany(Remisione::class);
    }
}
