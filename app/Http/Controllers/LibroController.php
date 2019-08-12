<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Libro;
use App\Entrada;
use App\Registro;
use PDF;

class LibroController extends Controller
{
    //Nuevo libro
    public function store(Request $request){
        $this->func_validar($request);
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

    //Función para validar los libros
    public function func_validar($request){
        $this->validate($request, [
            'titulo' => 'min:5|max:100|required|string',
            'ISBN' => 'min:5|max:100|required|string',
            'editorial' => 'required|min:5|max:100|string'
        ]);
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
        $libros = Libro::all();
        return response()->json($libros);
    }

    public function descargarLibros(){
        $libros = Libro::all();
        $data['libros'] = $libros;
        $pdf = PDF::loadView('inventario.libros', $data); 
        return $pdf->download('libros.pdf');
    }

    //Buscar libros por editorial
    public function porEditorial(){
        $queryEditorial = Input::get('queryEditorial');
        $libros = Libro::where('editorial','like','%'.$queryEditorial.'%')->get();
        return response()->json($libros);
    }

    //Actualizar libro
    public function update(Request $request){
        $this->func_validar($request);

        $libro = Libro::whereId($request->id)->first();
        try {
            \DB::beginTransaction();
                $libro->ISBN = $request->ISBN;
                $libro->titulo = $request->titulo;
                $libro->autor = $request->autor;
                $libro->editorial = $request->editorial;
                $libro->save();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($libro);
    }

    //Eliminar libro
    public function delete(){
        $id = Input::get('id');
        
        try {
            \DB::beginTransaction();
            $libro = Libro::whereId($id)->delete();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json(null, 200);
    }
}
