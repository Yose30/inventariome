<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Promotion;
use App\Remisione;
use App\Entrada;
use App\Adeudo;
use App\Compra;
use App\Regalo;
use App\Libro;
use App\Note;

class AlmacenController extends Controller
{
    public function entregas() {
        $remisiones = Remisione::where('estado', 'Iniciado')
            ->orderBy('id','desc')
            ->with('cliente')
            ->get();
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.entregas', compact('remisiones', 'responsables'));
    }

    public function pagos(){
        $remisiones = Remisione::where('total_pagar', '>', '0')
            ->where(function ($query) {
                $query->where('estado', 'Proceso')
                        ->orWhere('estado', 'Terminado');
            })->orderBy('id','desc')
            ->with('cliente')->get(); 
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.pagos', compact('remisiones', 'responsables'));
    }

    public function remisiones(){
        $remisiones = Remisione::whereNotIn('estado', ['Cancelado'])
            ->orderBy('id','desc')
            ->withCount('depositos')
            ->with('cliente')
            ->get();
        return view('almacen.remisiones', compact('remisiones'));
    }

    public function notas(){
        $notes = Note::orderBy('folio','desc')->get();
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.notas', compact('notes', 'responsables'));
    }

    public function promociones(){
        $promotions = Promotion::with('departures')->orderBy('folio','desc')->get();
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.promociones', compact('promotions', 'responsables'));
    }

    public function adeudos(){
        $adeudos = Adeudo::where('total_pendiente', '>', 0)->with('cliente')->with('abonos')->get();
        return view('almacen.adeudos', compact('adeudos'));
    }

    public function entradas(){
        $entradas = Entrada::with('registros')->orderBy('id','desc')->get();
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('almacen.entradas', compact('entradas', 'editoriales'));
    }
    
    public function libros(){
        $libros = Libro::orderBy('editorial', 'asc')->get();
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('almacen.libros', compact('libros', 'editoriales'));
    }

    public function pedidos(){
        $compras = Compra::orderBy('id','desc')->get();
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.pedidos', compact('compras', 'responsables'));
    }

    public function donaciones(){
        $regalos = Regalo::orderBy('id','desc')->get();
        $responsables = \DB::table('responsables')->orderBy('responsable', 'asc')->get();
        return view('almacen.donaciones', compact('regalos', 'responsables'));
    }
}
