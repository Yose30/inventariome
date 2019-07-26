<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cliente;
use App\Dato;
use App\Devolucione;
use App\Vendido;

class Remisione extends Model
{
    protected $fillable = [
        'id', 
        'cliente_id', 
        'tipo', 
        'total', 
        'total_devolucion', 
        'total_pagar', 
        'fecha_entrega', 
        'estado', 
        'fecha_creacion',
        'fecha_devolucion',
        'pagos'
    ];

    //Uno a muchos (Inversa)
    //Una remision solo puede pertenecer a un cliente
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }

    //Uno a muchos
    //UNa remision puede tener muchos datos
    public function datos(){
        return $this->hasMany(Dato::class);
    }

    //Uno a muchos
    //UNa remision puede tener muchas devoluciones
    public function devoluciones(){
        return $this->hasMany(Devolucione::class);
    }

    //Uno a muchos
    //Una remision puede tener muchas vendidos
    public function vendidos(){
        return $this->hasMany(Vendido::class);
    }
}
