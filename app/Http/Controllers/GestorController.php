<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Remisione;

class GestorController extends Controller
{
    public function remisiones() {
        $remisiones = Remisione::with('cliente')->orderBy('id','desc')->get();
        return view('gestor.remisiones', compact('remisiones'));
    }
}
