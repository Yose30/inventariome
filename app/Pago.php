<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Vendido;
use App\User;

class Pago extends Model
{
    protected $fillable = [
        'id', 
        'user_id',
        'vendido_id',
        'unidades',
        'pago', 
        'entregado_por'
    ];

    //Uno a muchos (Inverso)
    //Un pago solo puede pertenecer a un vendido
    public function vendido(){
        return $this->belongsTo(Vendido::class);
    }

    //Uno a muchos (Inverso)
    //Un pago solo puede pertenecer a un usuario
    public function user(){
        return $this->belongsTo(User::class);
    }
}
