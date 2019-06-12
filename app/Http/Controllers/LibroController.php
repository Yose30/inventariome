<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libro;

class LibroController extends Controller
{
    //Nuevo libro
    public function store(Request $request){
        $this->validate($request, [
            'titulo' => 'min:5|max:100|required|string',
            'ISBN' => 'min:5|max:100|required|string',
            'autor' => 'min:5|max:100|required|string',
            'editorial' => 'required|min:5|max:100|required|string',
            'edicion' => 'min:3|max:100|required|string',
        ]);
        
        $cliente = Libro::create($request->input());

        return response()->json(null, 200);
    }
}
