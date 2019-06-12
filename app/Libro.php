<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    protected $fillable = [
        'ISBN', 'titulo', 'autor', 'editorial', 'edicion'
    ];
}
