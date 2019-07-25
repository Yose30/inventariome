<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Entrada;
use App\User;

class Pago extends Model
{
    protected $fillable = [
        'id', 
        'user_id',
        'remision_id',
        'pago', 
        'tipo'
    ];

    public function entrada(){
        return $this->belongsTo(Entrada::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
