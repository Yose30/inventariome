<?php

namespace App\Http\Controllers;

use App\Exports\EditorialVendidosExport;
use App\Exports\VendidosDetallesExport;
use App\Exports\ClienteVendidosExport;
use App\Exports\LibroVendidosExport;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Excel;

class VendidoController extends Controller
{
    // OBTENER DETALLES DE LIBROS VENDIDOS
    // Función utilizada en VendidosComponent
    public function obtener_vendidos(){
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
        return response()->json($datos);
    }

    //OBTENER VENDIDOS POR FECHA (TODO)
    public function obtener_por_fecha(){
        $fecha1 = Input::get('fecha1');
        $fecha2 = Input::get('fecha2');
        
        $datos = \DB::table('vendidos')
            ->join('libros', 'vendidos.libro_id', '=', 'libros.id')
            ->whereBetween('vendidos.created_at', [$fecha1, $fecha2])
            ->where(function ($query) {
                $query->where('vendidos.unidades', '>', 0)
                        ->orWhere('vendidos.unidades_resta', '>', 0);
            })
            ->select(
                'libros.id as libro_id',
                'libros.titulo as libro',
                \DB::raw('SUM(unidades) as unidades_vendido'),
                \DB::raw('SUM(total) as total_vendido'),
                \DB::raw('SUM(unidades_resta) as unidades_pendiente'),
                \DB::raw('SUM(total_resta) as total_pendiente')
            )->groupBy('libros.titulo', 'libros.id')
            ->get();
        return response()->json($datos);
    }

    // BUSCAR LIBROS VENDIDOS POR CLIENTE
    public function obtener_cliente(){
        $cliente_id = Input::get('cliente_id');
        $datos = \DB::table('vendidos')
            ->join('remisiones', 'vendidos.remisione_id', '=', 'remisiones.id')
            ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
            ->join('libros', 'vendidos.libro_id', '=', 'libros.id')
            ->where('clientes.id', $cliente_id)
            ->where(function ($query) {
                $query->where('vendidos.unidades', '>', 0)
                        ->orWhere('vendidos.unidades_resta', '>', 0);
            })
            ->select(
                'libros.id as libro_id',
                'libros.titulo as libro',
                \DB::raw('SUM(vendidos.unidades) as unidades_vendido'),
                \DB::raw('SUM(vendidos.total) as total_vendido'),
                \DB::raw('SUM(vendidos.unidades_resta) as unidades_pendiente'),
                \DB::raw('SUM(vendidos.total_resta) as total_pendiente')
            )
            ->groupBy('libros.titulo', 'libros.id')
            ->orderBy('libros.titulo', 'asc')
            ->get();
        return response()->json($datos);
    }

    // BUSCAR LIBROS VENDIDOS POR CLIENTE Y FECHA
    public function cliente_por_fecha(){
        $fecha1 = Input::get('fecha1');
        $fecha2 = Input::get('fecha2');
        $cliente_id = Input::get('cliente_id');
        $datos = \DB::table('vendidos')
                    ->join('remisiones', 'vendidos.remisione_id', '=', 'remisiones.id')
                    ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
                    ->join('libros', 'vendidos.libro_id', '=', 'libros.id')
                    ->where('clientes.id', $cliente_id)
                    ->whereBetween('vendidos.created_at', [$fecha1, $fecha2])
                    ->where(function ($query) {
                        $query->where('vendidos.unidades', '>', 0)
                                ->orWhere('vendidos.unidades_resta', '>', 0);
                    })
                    ->select(
                        'libros.id as libro_id',
                        'libros.titulo as libro',
                        \DB::raw('SUM(vendidos.unidades) as unidades_vendido'),
                        \DB::raw('SUM(vendidos.total) as total_vendido'),
                        \DB::raw('SUM(vendidos.unidades_resta) as unidades_pendiente'),
                        \DB::raw('SUM(vendidos.total_resta) as total_pendiente')
                    )
                    ->groupBy('libros.titulo', 'libros.id')
                    ->orderBy('libros.titulo', 'asc')
                    ->get();
        return response()->json($datos);
    }

    // DESCARGAR REPORTE DE LIBROS VENIDOS POR CLIENTE
    public function downClienteEX($cliente_id, $fecha1, $fecha2){
        return Excel::download(new ClienteVendidosExport($cliente_id, $fecha1, $fecha2), 'reporte-cliente-vendidos.xlsx');
    }

    // OBTENER UNIDADES VENDIDAS POR LIBRO
    public function obtener_libro(){
        $libro_id = Input::get('libro_id');
        
        $vendidos = \DB::table('vendidos')
            ->join('remisiones', 'vendidos.remisione_id', '=', 'remisiones.id')
            ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
            ->where('vendidos.libro_id', $libro_id)
            ->where(function ($query) {
                $query->where('vendidos.unidades', '>', 0)
                        ->orWhere('vendidos.unidades_resta', '>', 0);
            })
            ->select(
                'clientes.name as cliente', 
                \DB::raw('SUM(vendidos.unidades) as unidades_vendido'),
                \DB::raw('SUM(vendidos.total) as total_vendido'),
                \DB::raw('SUM(vendidos.unidades_resta) as unidades_pendiente'),
                \DB::raw('SUM(vendidos.total_resta) as total_pendiente')
            )
            ->groupBy('clientes.name')
            ->get();
        return response()->json($vendidos);
    }

    // OBTENER UNIDADES VENDIDAS POR LIBRO Y POR FECHA
    public function libro_por_fecha(){
        $libro_id = Input::get('libro_id');
        $fecha1 = Input::get('fecha1');
        $fecha2 = Input::get('fecha2');

        $vendidos = \DB::table('vendidos')
            ->join('remisiones', 'vendidos.remisione_id', '=', 'remisiones.id')
            ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
            ->where('vendidos.libro_id', $libro_id)
            ->whereBetween('vendidos.created_at', [$fecha1, $fecha2])
            ->where(function ($query) {
                $query->where('vendidos.unidades', '>', 0)
                        ->orWhere('vendidos.unidades_resta', '>', 0);
            })
            ->select(
                'clientes.name as cliente', 
                \DB::raw('SUM(vendidos.unidades) as unidades_vendido'),
                \DB::raw('SUM(vendidos.total) as total_vendido'),
                \DB::raw('SUM(vendidos.unidades_resta) as unidades_pendiente'),
                \DB::raw('SUM(vendidos.total_resta) as total_pendiente')
            )
            ->groupBy('clientes.name')
            ->get();
        return response()->json($vendidos);
    }

    // DESCARGAR REPORTE POR LIBRO
    public function downLibroEX($libro_id, $fecha1, $fecha2){
        return Excel::download(new LibroVendidosExport($libro_id, $fecha1, $fecha2), 'reporte-libro-vendidos.xlsx');
    }

    //BUSCAR LIBROS VENDIDOS POR EDITORIAL
    public function obtener_editorial(){
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
            ->orderBy('libros.titulo', 'asc')
            ->get();
        return response()->json($datos);
    }

    // OBTENER VENDIDOS POR EDITORIAL Y FECHA
    public function editorial_por_fecha(){
        $fecha1 = Input::get('fecha1');
        $fecha2 = Input::get('fecha2');
        $editorial = Input::get('editorial');
        $datos = \DB::table('vendidos')
                ->join('libros', 'vendidos.libro_id', '=', 'libros.id')
                ->where('libros.editorial', $editorial)
                ->whereBetween('vendidos.created_at', [$fecha1, $fecha2])
                ->where(function ($query) {
                    $query->where('vendidos.unidades', '>', 0)
                            ->orWhere('vendidos.unidades_resta', '>', 0);
                })
                ->select(
                    'libros.id as libro_id',
                    'libros.titulo as libro',
                    \DB::raw('SUM(unidades) as unidades_vendido'),
                    \DB::raw('SUM(total) as total_vendido'),
                    \DB::raw('SUM(unidades_resta) as unidades_pendiente'),
                    \DB::raw('SUM(total_resta) as total_pendiente')
                )->groupBy('libros.titulo', 'libros.id')
                ->get();
        return response()->json($datos);
    }

    public function downEditorialEX($editorial, $fecha1, $fecha2){
        return Excel::download(new EditorialVendidosExport($editorial, $fecha1, $fecha2), 'reporte-editorial-vendidos.xlsx');
    }

    // DESCARGAR REPORTE DETALLADO DE LIBROS VENDIDOS
    public function downDetalladoEX($fecha1, $fecha2){    
        return Excel::download(new VendidosDetallesExport($fecha1, $fecha2), 'reporte-vendidos-detallado.xlsx');
    }

    // MOSTRAR DETALLES DE VENDIDOS
    public function detalles_vendidos(){
        $fecha1 = Input::get('fecha1');
        $fecha2 = Input::get('fecha2');
        $libro_id = Input::get('libro_id');

        if($fecha2 !== '0000-00-00'){
            $remisiones = \DB::table('vendidos')
                        ->join('remisiones', 'vendidos.remisione_id', '=', 'remisiones.id')
                        ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
                        ->whereBetween('vendidos.created_at', [$fecha1, $fecha2])
                        ->where('vendidos.libro_id', $libro_id)
                        ->where(function ($query) {
                            $query->where('vendidos.unidades', '>', 0)
                                    ->orWhere('vendidos.unidades_resta', '>', 0);
                        })
                        ->select(
                            'clientes.name as cliente', 
                            \DB::raw('SUM(vendidos.unidades) as unidades_vendido'),
                            \DB::raw('SUM(vendidos.total) as total_vendido'),
                            \DB::raw('SUM(vendidos.unidades_resta) as unidades_pendiente'),
                            \DB::raw('SUM(vendidos.total_resta) as total_pendiente')
                        )
                        ->groupBy('clientes.name')
                        ->get();
        }
        else{
            $remisiones = \DB::table('vendidos')
                ->join('remisiones', 'vendidos.remisione_id', '=', 'remisiones.id')
                ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
                ->where('vendidos.libro_id', $libro_id)
                ->where(function ($query) {
                    $query->where('vendidos.unidades', '>', 0)
                            ->orWhere('vendidos.unidades_resta', '>', 0);
                })
                ->select(
                    'clientes.name as cliente', 
                    \DB::raw('SUM(vendidos.unidades) as unidades_vendido'),
                    \DB::raw('SUM(vendidos.total) as total_vendido'),
                    \DB::raw('SUM(vendidos.unidades_resta) as unidades_pendiente'),
                    \DB::raw('SUM(vendidos.total_resta) as total_pendiente')
                )
                ->groupBy('clientes.name')
                ->get();

        }
        return response()->json($remisiones);
    }
}
