<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Remisione;
use App\Entrada;
use App\Cliente;
use App\Adeudo;
use App\Compra;
use App\Libro;
use App\Regalo;

class OficinaController extends Controller
{
    public function remisiones(){
        $remisiones = Remisione::with(['cliente:id,name'])
                    ->withCount('depositos')
                    ->orderBy('id','desc')
                    ->get();
        return view('oficina.remisiones', compact('remisiones'));
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
        $clientes = Cliente::orderBy('name', 'asc')->get();
        return view('oficina.clientes', compact('clientes'));
    }

    public function libros(){
        $libros = Libro::all();
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('oficina.libros', compact('libros', 'editoriales'));
    }

    public function adeudos(){
        $adeudos = Adeudo::with('cliente')->with('abonos')->get();
        return view('oficina.adeudos', compact('adeudos'));
    }

    public function entradas(){
        $entradas = Entrada::with('registros')->orderBy('id','desc')->get();
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('oficina.entradas', compact('entradas', 'editoriales'));
    }

    public function pedidos(){
        $compras = Compra::orderBy('id','desc')->get();
        return view('oficina.pedidos', compact('compras'));
    }

    public function donaciones(){
        $regalos = Regalo::orderBy('id','desc')->get();
        return view('oficina.donaciones', compact('regalos'));
    }
}
