<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Remisione;
use App\Dato;
use App\Devolucione;
use App\Entrada;
use App\Registro;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $remisiones = \DB::table('libros')
            ->join('devoluciones', 'libros.id', '=', 'devoluciones.libro_id')
            ->join('remisiones', 'devoluciones.remision_id', '=', 'remisiones.id')
            ->select('ISBN', 'titulo', \DB::raw('SUM(devoluciones.unidades_resta) as unidades_resta'), \DB::raw('SUM(devoluciones.total_resta) as total_resta'))
            ->groupBy('ISBN', 'titulo')
            ->get();
        
        dd($remisiones);
        return view('/home');
        
    }

}
