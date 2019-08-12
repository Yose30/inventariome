<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cliente;
use App\Abono;

class Adeudo extends Model
{
    protected $fillable = [
        'id', 
        'cliente_id', 
        'remision_num', 
        'fecha_adeudo', 
        'total_adeudo', 
        'total_abonos', 
        'total_pendiente',
        'total_devolucion'

    ];

    //Uno a muchos (Inversa)
    //Un adeudo solo puede pertenecer a un cliente
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    //Uno a muchos
    //Un adeudo puede tener muchos abonos
    public function abonos(){
        return $this->hasMany(Abono::class);
    }
}
