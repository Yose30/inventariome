<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Adeudo;

class Abono extends Model
{
    protected $fillable = [
        'id', 
        'adeudo_id', 
        'pago' 
    ];

    //Uno a muchos (Inversa)
    //Un abono solo puede pertenecer a un adeudo
    public function adeudo(){
        return $this->belongsTo(Adeudo::Adeudo);
    }
}
