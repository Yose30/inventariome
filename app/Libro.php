<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    protected $fillable = [
        'ISBN', 'clave', 'titulo', 'autor', 'editorial', 'edicion', 'costo_unitario'
    ];
}
