<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Remisione;
use App\Entrada;
use App\Cliente;
use App\Adeudo;
use App\Libro;

class OficinaController extends Controller
{
    public function remisiones(){
        $remisiones = Remisione::with('cliente:id,name')->orderBy('id','desc')->get();
        return view('oficina.remisiones', compact('remisiones'));
    }

    public function remision(){
        $clientes = Cliente::get();
        return view('oficina.remision', compact('clientes'));
    }

    public function pagos(){
        $remisiones = Remisione::where('total_pagar', '>', '0')
                ->where(function ($query) {
                    $query->where('estado', 'Proceso')
                            ->orWhere('estado', 'Terminado');
                })->orderBy('id','desc')
                ->with('cliente')->get(); 
        return view('oficina.pagos', compact('remisiones'));
    }

    public function clientes(){
        $clientes = Cliente::get();
        return view('oficina.clientes', compact('clientes'));
    }

    public function libros(){
        $libros = Libro::all();
        return view('oficina.libros', compact('libros'));
    }

    public function adeudos(){
        $adeudos = Adeudo::with('cliente')->with('abonos')->get();
        return view('oficina.adeudos', compact('adeudos'));
    }

    public function entradas(){
        $entradas = Entrada::with('registros')->orderBy('id','desc')->get();
        return view('oficina.entradas', compact('entradas'));
    }
}
