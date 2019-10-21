<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Libro;
use App\Entrada;
use App\Registro;
use PDF;
use App\Exports\LibrosExport;
use Maatwebsite\Excel\Facades\Excel;

class LibroController extends Controller
{
    //BUSCAR LIBROS POR EDITORIAL
    // Función utilizada en VendidosComponent
    public function porEditorialVendidos(){
        $editorial = Input::get('editorial');
        $datos = \DB::table('vendidos')
            ->join('libros', 'vendidos.libro_id', '=', 'libros.id')
            ->where('libros.editorial', $editorial)
            ->select(
                'libros.id as libro_id',
                'libros.titulo as libro',
                \DB::raw('SUM(unidades) as unidades_vendido'),
                \DB::raw('SUM(total) as total_vendido'),
                \DB::raw('SUM(unidades_resta) as unidades_pendiente'),
                \DB::raw('SUM(total_resta) as total_pendiente')
            )
            ->groupBy('libros.titulo', 'libros.id')
            ->get();
        return response()->json($datos);
    }

    // BUSCAR LIBRO POR ISBN
    // Función utilizada en
    // - AdeudosComponent - DevoluciónAdeudosComponent - EntradasComponent
    // - NewNotaComponent - PromocionesComponent - RemisionComponent
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

    // MOSTRAR LIBROS POR COINCIDENCIA DE LETRAS
    // Función utilizada en
    // - AdeudosComponent - DevoluciónAdeudosComponent - EntradasComponent
    // - NewNotaComponent - PromocionesComponent - RemisionComponent - LibrosComponent
    public function buscar(){
        $queryTitulo = Input::get('queryTitulo');
        $libros = \DB::table('libros')->select('id', 'ISBN', 'titulo', 'editorial', 'piezas')->where('titulo','like','%'.$queryTitulo.'%')->get();
        return response()->json($libros);
    }

    // BUSCAR LIBRO POR EDITORIAL
    // Función utilizada en LibrosComponent
    public function porEditorial(){
        $queryEditorial = Input::get('queryEditorial');
        $libros = Libro::where('editorial','like','%'.$queryEditorial.'%')->get();
        return response()->json($libros);
    }

    // DESCARGAR EN FORMATO EXCEL LOS LIBROS
    // Función utilizada en LibrosComponent
    public function downloadExcel($editorial){
        return Excel::download(new LibrosExport($editorial), 'libros.xlsx');
    }

    // GUARDAR NUEVO LIBRO
    // Función utilizada en NewLibroComponent
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
            'titulo' => 'min:5|max:100|required|string|unique:libros',
            'ISBN' => 'min:5|max:100|required|string|unique:libros',
            'editorial' => 'required|min:5|max:100|string'
        ]);
    }

    // ACTUALIZAR DATOS DE LIBRO
    // Función utilizada en EditarLibroComponent
    public function update(Request $request){
        $libro = Libro::whereId($request->id)->first();
        $libro->ISBN = 'CHANGE';
        $libro->titulo = 'CHANGE';
        $libro->save();     
        $this->func_validar($request);
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

    // ELIMINAR LIBRO (FUNCIÓN ELIMINADA DE COMPONENT)
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
