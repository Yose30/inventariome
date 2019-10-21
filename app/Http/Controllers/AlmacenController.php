<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Promotion;
use App\Remisione;
use App\Entrada;
use App\Adeudo;
use App\Libro;
use App\Note;

class AlmacenController extends Controller
{
    public function entregas() {
        $remisiones = Remisione::where('estado', 'Iniciado')
            ->orderBy('id','desc')
            ->with('cliente')
            ->get();

        return view('almacen.entregas', compact('remisiones'));
    }

    public function pagos(){
        $remisiones = Remisione::where('total_pagar', '>', '0')
            ->where(function ($query) {
                $query->where('estado', 'Proceso')
                        ->orWhere('estado', 'Terminado');
            })->orderBy('id','desc')
            ->with('cliente')->get(); 

        return view('almacen.pagos', compact('remisiones'));
    }

    public function remisiones(){
        $remisiones = Remisione::whereNotIn('estado', ['Cancelado'])
            ->orderBy('id','desc')
            ->with('cliente')
            ->get();
        return view('almacen.remisiones', compact('remisiones'));
    }

    public function notas(){
        $notes = Note::orderBy('folio','desc')->get();
        return view('almacen.notas', compact('notes'));
    }

    public function promociones(){
        $promotions = Promotion::with('departures')->orderBy('folio','desc')->get();
        return view('almacen.promociones', compact('promotions'));
    }

    public function adeudos(){
        $adeudos = Adeudo::where('total_pendiente', '>', 0)->with('cliente')->with('abonos')->get();
        return view('almacen.adeudos', compact('adeudos'));
    }

    public function entradas(){
        $entradas = Entrada::with('registros')->orderBy('id','desc')->get();
        return view('almacen.entradas', compact('entradas'));
    }
    
    public function libros(){
        $libros = Libro::all();
        return view('almacen.libros', compact('libros'));
    }
}
