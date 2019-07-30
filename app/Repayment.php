<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Register;
use App\Note;

class Repayment extends Model
{
    //Uno a uno (Inversa)
    //Un repayment solo puede pertenecer a un register
    public function register(){
        return $this->belongsTo(Register::class);
    }

    //Uno a muchos (Inversa)
    //Un repayment solo puede pertenecer a una devolución
    public function note(){
        return $this->belongsTo(Note::class);
    }
}
