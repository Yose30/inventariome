<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Promotion;
use App\Remisione;
use App\Entrada;
use App\Cliente;
use App\Adeudo;
use App\Libro;
use App\Note;

class AdministradorController extends Controller
{
    public function remisiones() {
        $remisiones = Remisione::with('cliente:id,name')->orderBy('id','desc')->get();
        return view('administrador.remisiones', compact('remisiones'));
    }

    public function vendidos(){
        $datos = \DB::table('vendidos')
                ->join('libros', 'vendidos.libro_id', '=', 'libros.id')
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
        return view('administrador.vendidos', compact('datos'));
    }

    public function notas(){
        $notes = Note::orderBy('folio','desc')->get();
        return view('administrador.notas', compact('notes'));
    }

    public function promociones(){
        $promotions = Promotion::with('departures')->orderBy('folio','desc')->get();
        return view('administrador.promociones', compact('promotions'));
    }

    public function adeudos(){
        $adeudos = Adeudo::with('cliente')->with('abonos')->get();
        return view('administrador.adeudos', compact('adeudos'));
    }

    public function entradas(){
        $entradas = Entrada::with('registros')->orderBy('id','desc')->get();
        return view('administrador.entradas', compact('entradas'));
    }

    public function libros(){
        $libros = Libro::all();
        return view('administrador.libros', compact('libros'));
    }

    public function clientes(){
        $clientes = Cliente::get();
        return view('administrador.clientes', compact('clientes'));
    }
}
