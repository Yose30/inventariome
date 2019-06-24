<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Libro;

class LibroController extends Controller
{
    //Nuevo libro
    public function store(Request $request){
        $this->validate($request, [
            'titulo' => 'min:5|max:100|required|string',
            // 'clave' => 'min:5|max:20|required|string',
            'ISBN' => 'min:5|max:100|required|string',
            'autor' => 'min:5|max:100|required|string',
            'editorial' => 'required|min:5|max:100|required|string',
            'edicion' => 'min:3|max:100|required|string',
            'costo_unitario' => 'required|min:3',
        ]);
        
        try {
            \DB::beginTransaction();

            $cliente = Libro::create($request->input());

            \DB::commit();

            return response()->json(null, 200);
            
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    //Mostrar libros
    public function buscar(){
        $queryTitulo = Input::get('queryTitulo');
        $libros = Libro::where('titulo','like','%'.$queryTitulo.'%')->get();
        return response()->json($libros);
    }
    //Buscar libro
    public function show(){
        $isbn = Input::get('isbn');
        $libro = Libro::where('ISBN', $isbn)->first();
        $datos = [
            'ISBN' => $libro->ISBN,
            'titulo' => $libro->titulo,
            'costo_unitario' => $libro->costo_unitario,
            'unidades' => 0,
            'total' => 0
        ];
        return response()->json($datos);
    }
}
