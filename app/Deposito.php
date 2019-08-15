<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Remisione;

class Deposito extends Model
{
    protected $fillable = [
        'id', 
        'remision_id', 
        'pago' 
    ];

    //Uno a muchos (inversa)
    //Un deposito solo puede pertencer a una remisión
    public function remisione(){
        return $this->belongsTo(Remisione::class);
    }
}
