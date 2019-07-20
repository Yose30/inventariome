<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Libro;
use App\Entrada;
use App\Registro;

class LibroController extends Controller
{
    //Nuevo libro
    public function store(Request $request){
        $this->validate($request, [
            'titulo' => 'min:5|max:100|required|string',
            'ISBN' => 'min:5|max:100|required|string',
            'editorial' => 'required|min:5|max:100|string',
            'costo_unitario' => 'required|min:3',
        ]);
        try {
            \DB::beginTransaction();
                $libro = Libro::create($request->input());
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($libro);
    }

    //Mostrar libros
    public function buscar(){
        $queryTitulo = Input::get('queryTitulo');
        $libros = \DB::table('libros')->select('id', 'ISBN', 'titulo', 'editorial', 'piezas')->where('titulo','like','%'.$queryTitulo.'%')->get();
        return response()->json($libros);
    }
    //Buscar libro
    public function show(){
        $isbn = Input::get('isbn');
        $libro = Libro::where('ISBN', $isbn)->first();
        $datos = [
            'id' => $libro->id,
            'ISBN' => $libro->ISBN,
            'titulo' => $libro->titulo,
            'costo_unitario' => 0,
            'unidades' => 0,
            'total' => 0,
            'piezas' => $libro->piezas
        ];
        return response()->json($datos);
    }

    //Obtener todos los libros
    public function allLibros(){
        $libros = \DB::table('libros')->select('id', 'titulo', 'editorial', 'piezas')->get();
        return response()->json($libros);
    }

    public function porEditorial(){
        $queryEditorial = Input::get('queryEditorial');
        $libros = \DB::table('libros')->select('id', 'titulo', 'editorial', 'piezas')->where('editorial','like','%'.$queryEditorial.'%')->get();
        return response()->json($libros);
    }
}
