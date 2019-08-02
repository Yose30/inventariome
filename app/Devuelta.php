<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Departure;

class Devuelta extends Model
{
    protected $fillable = [
        'id', 
        'departure_id',  
        'unidades'
    ];

    //Uno a uno (Inversa)
    public function departure(){
        return $this->belongsTo(Departure::class);
    }
}
