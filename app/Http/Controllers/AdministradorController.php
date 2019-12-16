<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Promotion;
use App\Remisione;
use App\Vendido;
use App\Entrada;
use App\Cliente;
use App\Compra;
use App\Adeudo;
use App\Regalo;
use App\Libro;
use App\Note;

class AdministradorController extends Controller
{
    public function remisiones() {
        $remisiones = Remisione::with('cliente:id,name')->withCount('depositos')->orderBy('id','desc')->get();
        return view('administrador.remisiones', compact('remisiones'));
    }

    public function vendidos(){
        $datos = \DB::table('vendidos')
                ->join('libros', 'vendidos.libro_id', '=', 'libros.id')
                ->where('unidades', '>', 0)->orWhere('unidades_resta', '>', 0)
                ->select(
                    'libros.id as libro_id',
                    'libros.titulo as libro',
                    \DB::raw('SUM(unidades) as unidades_vendido'), 
                    \DB::raw('SUM(total) as total_vendido'),
                    \DB::raw('SUM(unidades_resta) as unidades_pendiente'),
                    \DB::raw('SUM(total_resta) as total_pendiente')
                )
                ->groupBy('libros.titulo', 'libros.id')
                ->orderBy('libros.titulo', 'asc')
                ->get();
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('administrador.vendidos', compact('datos', 'editoriales'));
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
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('administrador.entradas', compact('entradas', 'editoriales'));
    }

    public function libros(){
        $libros = Libro::orderBy('editorial','asc')->get();
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('administrador.libros', compact('libros', 'editoriales'));
    }

    public function clientes(){
        $clientes = Cliente::orderBy('name', 'asc')->get();
        return view('administrador.clientes', compact('clientes'));
    }

    public function pedidos(){
        $compras = Compra::orderBy('id','desc')->get();
        return view('administrador.pedidos', compact('compras'));
    }

    public function donaciones(){
        $regalos = Regalo::orderBy('id','desc')->get();
        return view('administrador.donaciones', compact('regalos'));
    }

    public function movimientos(){
        $editoriales = \DB::table('editoriales')->orderBy('editorial', 'asc')->get();
        return view('administrador.movimientos', compact('editoriales'));
    }
}
