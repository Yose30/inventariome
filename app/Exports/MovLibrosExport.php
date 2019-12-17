<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MovLibrosExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($editorial, $tipo)
    {
        $this->editorial = $editorial;
        $this->tipo = $tipo;
    }

    public function view(): View
    {
        $movimientos = $this->get_movimientos();
        return view('download.excel.libros.movimientos', [
            'movimientos' => $movimientos,
            'tipo' => $this->tipo
        ]);
    }

    public function get_movimientos(){
        if($this->editorial === 'TODO'){
            if($this->tipo === 'unidades')
                $registros = $this->busqueda_unidades_gral();
            if($this->tipo === 'monto')
                $registros = $this->busqueda_monto_gral();
        } else{
            if($this->tipo === 'unidades')
                $registros = $this->unidades_editorial_gral($this->editorial);
            if($this->tipo === 'monto')
                $registros = $this->monto_editorial_gral($this->editorial);
        }

        if($this->tipo === 'unidades')
            $movimientos = $this->busqueda_unidades($registros);
        if($this->tipo === 'monto')
            $movimientos = $this->busqueda_monto($registros);
        return $movimientos;
    }

    public function busqueda_unidades_gral(){
        $registros = \DB::table('registros')
                ->join('libros', 'registros.libro_id', '=', 'libros.id')
                ->select(
                    'libro_id as libro_id',
                    'libros.titulo as libro',
                    \DB::raw('SUM(unidades) as entradas'),
                    'libros.piezas as existencia'
                )->groupBy('libro_id', 'libros.titulo', 'libros.piezas')
                ->orderBy('libros.titulo', 'asc')
                ->get();
        return $registros;
    }

    public function unidades_editorial_gral($editorial){
        $registros = \DB::table('registros')
            ->join('libros', 'registros.libro_id', '=', 'libros.id')
            ->where('libros.editorial', $editorial)
            ->select(
                'libro_id as libro_id',
                'libros.titulo as libro',
                \DB::raw('SUM(unidades) as entradas'),
                'libros.piezas as existencia'
            )->groupBy('libro_id', 'libros.titulo', 'libros.piezas')
            ->orderBy('libros.titulo', 'asc')
            ->get();
        return $registros;
    }

    public function busqueda_unidades($registros){
        // ENTRADAS
        $devoluciones = \DB::table('devoluciones')
                    ->select('libro_id as libro_id' ,\DB::raw('SUM(unidades) as devoluciones'))
                    ->groupBy('libro_id')
                    ->get();
        $notas_entrada = \DB::table('registers')
                    ->select('libro_id as libro_id' ,\DB::raw('SUM(unidades_devuelto) as notas_entrada'))
                    ->groupBy('libro_id')
                    ->get();
        // SALIDAS
        $remisiones = \DB::table('datos')
                    ->join('remisiones', 'datos.remisione_id', '=', 'remisiones.id')
                    ->whereNotIn('remisiones.estado', ['Cancelado'])
                    ->select('libro_id as libro_id' ,\DB::raw('SUM(unidades) as remisiones'))
                    ->groupBy('libro_id')
                    ->get();
        $notas_salida = \DB::table('registers')
                    ->select('libro_id as libro_id' ,\DB::raw('SUM(unidades) as notas_salida'))
                    ->groupBy('libro_id')
                    ->get();
        $pedidos = \DB::table('pedidos')
                    ->select('libro_id as libro_id' ,\DB::raw('SUM(unidades) as pedidos'))
                    ->groupBy('libro_id')
                    ->get();
        $promociones = \DB::table('departures')
                    ->select('libro_id as libro_id' ,\DB::raw('SUM(unidades) as promociones'))
                    ->groupBy('libro_id')
                    ->get();
        $donaciones = \DB::table('donaciones')
                    ->select('libro_id as libro_id' ,\DB::raw('SUM(unidades) as donaciones'))
                    ->groupBy('libro_id')
                    ->get();

        $movimientos = array();
        foreach($registros as $registro){
            $relacion = $this->assign_array($registro, $devoluciones, $notas_entrada, $remisiones, $notas_salida, $pedidos, $promociones, $donaciones, 'unidades');
            array_push($movimientos, $relacion);
        }   
        return $movimientos;
    }

    public function busqueda_monto_gral(){
        $registros = \DB::table('registros')
                ->join('libros', 'registros.libro_id', '=', 'libros.id')
                ->select(
                    'libro_id as libro_id',
                    'libros.titulo as libro',
                    \DB::raw('SUM(total) as entradas')
                )->groupBy('libro_id', 'libros.titulo')
                ->orderBy('libros.titulo', 'asc')
                ->get();
        return $registros;
    }

    public function monto_editorial_gral($editorial){
        $registros = \DB::table('registros')
            ->join('libros', 'registros.libro_id', '=', 'libros.id')
            ->where('libros.editorial', $editorial)
            ->select(
                'libro_id as libro_id',
                'libros.titulo as libro',
                \DB::raw('SUM(total) as entradas')
            )->groupBy('libro_id', 'libros.titulo')
            ->orderBy('libros.titulo', 'asc')
            ->get();
        return $registros;
    }

    public function busqueda_monto($registros){
        // ENTRADAS
        $devoluciones = \DB::table('devoluciones')
                ->select('libro_id as libro_id' ,\DB::raw('SUM(total) as devoluciones'))
                ->groupBy('libro_id')
                ->get();
        $notas_entrada = \DB::table('registers')
                ->select('libro_id as libro_id' ,\DB::raw('SUM(total_devuelto) as notas_entrada'))
                ->groupBy('libro_id')
                ->get();
        // // SALIDAS
        $remisiones = \DB::table('datos')
                ->join('remisiones', 'datos.remisione_id', '=', 'remisiones.id')
                ->whereNotIn('remisiones.estado', ['Cancelado'])
                ->select('libro_id as libro_id', \DB::raw('SUM(datos.total) as remisiones'))
                ->groupBy('libro_id')
                ->get();
        $notas_salida = \DB::table('registers')
                ->select('libro_id as libro_id' ,\DB::raw('SUM(total) as notas_salida'))
                ->groupBy('libro_id')
                ->get();
        $pedidos = \DB::table('pedidos')
                ->select('libro_id as libro_id' ,\DB::raw('SUM(total) as pedidos'))
                ->groupBy('libro_id')
                ->get();
        $promociones = [];
        $donaciones = [];
        $movimientos = array();
        foreach($registros as $registro){
            $relacion = $this->assign_array($registro, $devoluciones, $notas_entrada, $remisiones, $notas_salida, $pedidos, $promociones, $donaciones, 'monto');
            array_push($movimientos, $relacion);
        }   
        return $movimientos;
    }

    public function assign_array($registro, $devoluciones, $notas_entrada, $remisiones, $notas_salida, $pedidos, $promociones, $donaciones, $tipo){
        if($tipo === 'unidades'){
            $relacion = [
                'libro' => '',
                'entradas' => 0,
                'devoluciones' => 0,
                'notas_entrada' => 0,
                'remisiones' => 0,
                'notas_salida' => 0,
                'pedidos' => 0,
                'promociones' => 0,
                'donaciones' => 0,
                'existencia' => 0,
            ];
            $relacion['existencia'] = $registro->existencia;
            foreach($promociones as $promocion){
                if($registro->libro_id === $promocion->libro_id)
                    $relacion['promociones'] = $promocion->promociones;
            }
            foreach($donaciones as $donacion){
                if($registro->libro_id === $donacion->libro_id)
                    $relacion['donaciones'] = $donacion->donaciones;
            }
        }
        if($tipo === 'monto'){
            $relacion = [
                'libro' => '',
                'entradas' => 0,
                'remisiones' => 0,
                'devoluciones' => 0,
                'notas_entrada' => 0,
                'notas_salida' => 0,
                'pedidos' => 0
            ];
        }
        $relacion['libro'] = $registro->libro;
        $relacion['entradas'] = $registro->entradas;
        foreach($devoluciones as $devolucion){
            if($registro->libro_id === $devolucion->libro_id)
                $relacion['devoluciones'] = $devolucion->devoluciones;
        }
        foreach($notas_entrada as $nota_e){
            if($registro->libro_id === $nota_e->libro_id)
                $relacion['notas_entrada'] = $nota_e->notas_entrada;
        }
        foreach($remisiones as $remision){
            if($registro->libro_id === $remision->libro_id)
                $relacion['remisiones'] = $remision->remisiones;
        }
        foreach($notas_salida as $nota_s){
            if($registro->libro_id === $nota_s->libro_id)
                $relacion['notas_salida'] = $nota_s->notas_salida;
        }
        foreach($pedidos as $pedido){
            if($registro->libro_id === $pedido->libro_id)
                $relacion['pedidos'] = $pedido->pedidos;
        }
        return $relacion;
    }
}
