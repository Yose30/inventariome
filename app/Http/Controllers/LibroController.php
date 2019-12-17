<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Libro;
use App\Entrada;
use App\Registro;
use PDF;
use Carbon\Carbon;
use App\Exports\LibrosExport;
use App\Exports\MovLibrosExport;
use App\Exports\MovFechasExport;
use Maatwebsite\Excel\Facades\Excel;

class LibroController extends Controller
{

    // BUSCAR LIBRO POR ISBN
    // Función utilizada en
    // - AdeudosComponent - DevoluciónAdeudosComponent - EntradasComponent
    // - NewNotaComponent - PromocionesComponent - RemisionComponent
    public function show(){
        $isbn = Input::get('isbn');
        $libro = Libro::where('ISBN', $isbn)->first();
        $datos = $this->assign_registers($libro);
        return response()->json($datos);
    }

    public function isbn_por_editorial(){
        $isbn = Input::get('isbn');
        $editorial = Input::get('editorial');
        $libro = Libro::where('ISBN', $isbn)->where('editorial','like','%'.$editorial.'%')->first();
        $datos = $this->assign_registers($libro);
        return response()->json($datos);
    }

    public function assign_registers($libro){
        return $datos = [
                'id' => $libro->id,
                'ISBN' => $libro->ISBN,
                'titulo' => $libro->titulo,
                'editorial' => $libro->editorial,
                'piezas' => $libro->piezas,
                'costo_unitario' => 0,
                'unidades' => 0,
                'total' => 0,
            ];
    }

    // MOSTRAR LIBROS POR COINCIDENCIA DE LETRAS
    // Función utilizada en
    // - AdeudosComponent - DevoluciónAdeudosComponent - EntradasComponent
    // - NewNotaComponent - PromocionesComponent - RemisionComponent - LibrosComponent
    public function buscar(){
        $queryTitulo = Input::get('queryTitulo');
        $libros = \DB::table('libros')
                    ->select('id', 'ISBN', 'titulo', 'editorial', 'piezas')
                    ->where('titulo','like','%'.$queryTitulo.'%')
                    ->orderBy('editorial', 'asc')->get();
        return response()->json($libros);
    }

    public function libros_por_editorial(){
        $queryTitulo = Input::get('queryTitulo');
        $editorial = Input::get('editorial');
        $libros = \DB::table('libros')
                    ->select('id', 'ISBN', 'titulo', 'editorial', 'piezas')
                    ->where('editorial','like','%'.$editorial.'%')
                    ->where('titulo','like','%'.$queryTitulo.'%')
                    ->orderBy('editorial', 'asc')->get();
        return response()->json($libros);
    }

    // BUSCAR LIBRO POR EDITORIAL
    // Función utilizada en LibrosComponent
    public function porEditorial(){
        $queryEditorial = Input::get('queryEditorial');
        if($queryEditorial === 'TODO'){
            $libros = Libro::orderBy('editorial', 'asc')->get();
        }
        else{
            $libros = Libro::where('editorial','like','%'.$queryEditorial.'%')->orderBy('titulo', 'asc')->get();
        }
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
                $libro = Libro::create([
                    'ISBN' => $request->ISBN,
                    'titulo' => strtoupper($request->titulo),
                    'autor' => strtoupper($request->autor),
                    'editorial' => $request->editorial
                ]);

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
            'ISBN' => 'required|numeric|max:9999999999999|min:1000000000000|unique:libros',
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
                $libro->update([
                    'ISBN' => $request->ISBN,
                    'titulo' => strtoupper($request->titulo),
                    'autor' => strtoupper($request->autor),
                    'editorial' => $request->editorial
                ]);
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

    // MOSTRAR MOVIMIENTOS DE UN LIBRO
    public function movimientos_todos(){
        $registros = $this->busqueda_unidades_gral();
        $movimientos = $this->busqueda_unidades($registros);
        
        return response()->json($movimientos);
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

    public function movimientos_por_edit(){
        $editorial = Input::get('queryEMov');
        $tipo = Input::get('tipo');
        if($editorial === 'TODO'){
            if($tipo === 'unidades')
                $registros = $this->busqueda_unidades_gral();
            if($tipo === 'monto')
                $registros = $this->busqueda_monto_gral();
        } else{
            if($tipo === 'unidades')
                $registros = $this->unidades_editorial_gral($editorial);
            if($tipo === 'monto')
                $registros = $this->monto_editorial_gral($editorial);
        }

        if($tipo === 'unidades')
            $movimientos = $this->busqueda_unidades($registros);
        if($tipo === 'monto')
            $movimientos = $this->busqueda_monto($registros);
        return response()->json($movimientos);
    }

    public function download_movimientos($editorial, $tipo){
        return Excel::download(new MovLibrosExport($editorial, $tipo), 'movimientos.xlsx');
    }

    public function down_fechaCategoria($inicio, $final, $categoria){
        return Excel::download(new MovFechasExport($inicio, $final, $categoria), $categoria.'.xlsx');
    }

    public function movimientos_por_fecha(){
        $categoria = Input::get('categoria');
        $inicio = Input::get('inicio');
        $final = Input::get('final');

        $fechas = $this->format_date($inicio, $final);
        $fecha1 = $fechas['inicio'];
        $fecha2 = $fechas['final'];

        // ENTRADAS
        if($categoria === 'ENTRADAS'){
            $datos = \DB::table('registros')
                ->join('libros', 'registros.libro_id', '=', 'libros.id')
                ->whereBetween('registros.created_at', [$fecha1, $fecha2])
                ->select(
                    // 'libro_id as libro_id',
                    'libros.titulo as libro',
                    \DB::raw('SUM(unidades) as unidades'),
                    \DB::raw('SUM(total) as total')
                )->groupBy('libro_id', 'libros.titulo')
                ->orderBy('libros.titulo', 'asc')
                ->get();
        }
        if($categoria === 'DEVOLUCIONES'){
            $datos = \DB::table('devoluciones')
                ->join('libros', 'devoluciones.libro_id', '=', 'libros.id')
                ->whereBetween('devoluciones.created_at', [$fecha1, $fecha2])
                ->whereNotIn('devoluciones.unidades', [0])
                ->select(
                    'libros.titulo as libro',
                    \DB::raw('SUM(unidades) as unidades'),
                    \DB::raw('SUM(total) as total')
                )
                ->orderBy('libros.titulo', 'asc')
                ->groupBy('libro_id', 'libros.titulo')
                ->get();
        }
        if($categoria === 'NOTASDEV'){
            $datos = \DB::table('registers')
                    ->join('libros', 'registers.libro_id', '=', 'libros.id')
                    ->whereNotIn('registers.unidades_devuelto', [0])
                    ->select(
                        'libros.titulo as libro',
                        \DB::raw('SUM(unidades_devuelto) as unidades'),
                        \DB::raw('SUM(total_devuelto) as total')
                    )->whereBetween('registers.created_at', [$fecha1, $fecha2])
                    ->orderBy('libros.titulo', 'asc')
                    ->groupBy('libro_id', 'libros.titulo')
                    ->get();
        }
        // SALIDAS
        if($categoria === 'REMISIONES'){
            $datos = \DB::table('datos')
                    ->join('remisiones', 'datos.remisione_id', '=', 'remisiones.id')
                    ->join('libros', 'datos.libro_id', '=', 'libros.id')
                    ->whereNotIn('remisiones.estado', ['Cancelado'])
                    ->whereBetween('datos.created_at', [$fecha1, $fecha2])
                    ->select(
                        // 'libro_id as libro_id',
                        'libros.titulo as libro',
                        \DB::raw('SUM(datos.unidades) as unidades'),
                        \DB::raw('SUM(datos.total) as total')
                    )
                    ->orderBy('libros.titulo', 'asc')
                    ->groupBy('libro_id', 'libros.titulo')
                    ->get();
        }
        if($categoria === 'NOTAS'){
            $datos = \DB::table('registers')
                    ->join('libros', 'registers.libro_id', '=', 'libros.id')
                    ->select(
                        'libros.titulo as libro',
                        \DB::raw('SUM(unidades) as unidades'),
                        \DB::raw('SUM(total) as total')
                    )->whereBetween('registers.created_at', [$fecha1, $fecha2])
                    ->orderBy('libros.titulo', 'asc')
                    ->groupBy('libro_id', 'libros.titulo')
                    ->get();
        }
        if($categoria === 'PEDIDOS'){
            $datos = \DB::table('pedidos')
                    ->join('libros', 'pedidos.libro_id', '=', 'libros.id')
                    ->select(
                        // 'libro_id as libro_id',
                        'libros.titulo as libro',
                        \DB::raw('SUM(unidades) as unidades'),
                        \DB::raw('SUM(total) as total')
                    )->whereBetween('pedidos.created_at', [$fecha1, $fecha2])
                    ->orderBy('libros.titulo', 'asc')
                    ->groupBy('libro_id', 'libros.titulo')
                    ->get();
        }
        if($categoria === 'PROMOCIONES'){
            $datos = \DB::table('departures')
                    ->join('libros', 'departures.libro_id', '=', 'libros.id')
                    ->select('libros.titulo as libro', \DB::raw('SUM(unidades) as unidades'))
                    ->whereBetween('departures.created_at', [$fecha1, $fecha2])
                    ->orderBy('libros.titulo', 'asc')
                    ->groupBy('libro_id', 'libros.titulo')
                    ->get();
        }
        if($categoria === 'DONACIONES'){
            $datos = \DB::table('donaciones')
                    ->join('libros', 'donaciones.libro_id', '=', 'libros.id')
                    ->select('libros.titulo as libro', \DB::raw('SUM(unidades) as unidades'))
                    ->whereBetween('donaciones.created_at', [$fecha1, $fecha2])
                    ->orderBy('libros.titulo', 'asc')
                    ->groupBy('libro_id', 'libros.titulo')
                    ->get();
        }
        return response()->json($datos);
    }

    public function format_date($fecha1, $fecha2){
        $inicio = new Carbon($fecha1);
        $final 	= new Carbon($fecha2);
        $inicio = $inicio->format('Y-m-d 00:00:00');
        $final 	= $final->format('Y-m-d 23:59:59');

        $fechas = [
            'inicio' => $inicio,
            'final' => $final
        ];

        return $fechas;
    }
}
