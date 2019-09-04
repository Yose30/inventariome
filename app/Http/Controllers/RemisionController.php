<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use NumerosEnLetras;
use App\Devolucione;
use Carbon\Carbon;
use App\Remisione;
use App\Deposito;
use App\Vendido;
use App\Cliente;
use App\Libro;
use App\Dato;
use App\Pago;
use PDF;

class RemisionController extends Controller
{
    public function registro(Request $request){
        // if($request->remision_id != 0){
        //     $remision = $request->remision_id;
        // }
        // else{
        //     $remision = Remisione::all()->count() + 1;
        // }
        
        // try {

        //     \DB::beginTransaction();

        //     $dato = Dato::create([
        //         'remision_id' => $remision,
        //         'libro_id'  => $request->id,
        //         'costo_unitario' => $request->costo_unitario,
        //         'unidades'  => $request->unidades,
        //         'total'     => $request->total
        //     ]);

        //     $libro = Libro::whereId($dato->libro_id)->first();
        //     $libro->update(['piezas' => $libro->piezas - $dato->unidades]);
            
        //     \DB::commit();

        //     return response()->json(['dato' => $dato, 'libro' => $dato->libro]);

        // } catch (Exception $e) {
        //     \DB::rollBack();
        //     return response()->json($exception->getMessage());
		// }
    }

    public function store(Request $request){
        try {
            \DB::beginTransaction();
            
            $remision = Remisione::create([
                'cliente_id' => $request->cliente_id,
                'total' => $request->total,
                'fecha_entrega' => $request->fecha_entrega,
                'estado' => 1,
                'fecha_creacion' => Carbon::now()->format('Y-m-d'),
                'fecha_devolucion' => Carbon::now()->format('Y-m-d')
            ]);

            foreach($request->registros as $registro){
                $dato = Dato::create([
                    'remisione_id' => $remision->id,
                    'libro_id'  => $registro['id'],
                    'costo_unitario' => $registro['costo_unitario'],
                    'unidades'  => $registro['unidades'],
                    'total'     => $registro['total'],
                    'estado'    => 'Terminado'
                ]);
    
                $libro = Libro::whereId($dato->libro_id)->first();
                $libro->update(['piezas' => $libro->piezas - $dato->unidades]);
            }
            
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($remision);
    }

    public function show(){
        $numero = Input::get('numero');
        $remision = Remisione::whereId($numero)->first();
        $datos = Dato::where('remisione_id', $remision->id)->with('libro')->get();
        $devoluciones = Devolucione::where('remisione_id', $remision->id)->with('libro', 'dato')->get();
        $vendidos = Vendido::where('remisione_id', $remision->id)->with('libro', 'dato', 'pagos')->get();
        return response()->json([
            'remision' => $remision, 
            'datos' => $datos, 
            'devoluciones' => $devoluciones, 
            'vendidos' => $vendidos
        ]);
    }

    public function actualizar(Request $request){
        $remision = Remisione::whereId($request->id)->first();
        
        try {
            \DB::beginTransaction();
            $remision->fecha_entrega = $request->fecha_entrega;
            $remision->total = $request->total;
            $remision->save();
            \DB::commit();
            $this->concluir_remision($remision->id);
            return response()->json($remision);
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function concluir_remision($id){
        $datos = Dato::where('remisione_id', $id)->where('estado', 'Eliminado')->get();

        foreach($datos as $dato){
            $libro = Libro::whereId($dato->libro_id)->first();
            $libro->update(['piezas' => $libro->piezas + $dato->unidades]);
        }

        Dato::where('remisione_id', $id)->where('estado', 'Eliminado')->delete();
        Dato::where('remisione_id', $id)->update(['estado' => 'Terminado']);
    }

    public function eliminar(Request $request){
        try {
            \DB::beginTransaction();

            $dato = Dato::whereId($request->id)->update(['estado' => 'Eliminado']);

            \DB::commit();
            
            return response()->json($dato);
        
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function todos(){
        $remisiones = Remisione::with('cliente')->orderBy('id','desc')->get();
        return response()->json($remisiones);
    } 

    public function get_iniciados(){
        $remisiones = Remisione::where('estado', '!=', 'Cancelado')
                            ->orderBy('id','desc')
                            ->with('cliente')
                            ->get();
        return response()->json($remisiones);
    } 

    public function por_numero(){
        $num_remision = Input::get('num_remision');
        $remision = Remisione::whereId($num_remision)->with('cliente')->first();
        $cliente = Cliente::whereId($remision->cliente_id)->first();
        return response()->json(['remision' => $remision, 'cliente_nombre' => $cliente->name]);
    } 

    public function por_cliente(){
        $id = Input::get('id');
        $inicio = Input::get('inicio');
        $final = Input::get('final');

        if($inicio != '' && $final != ''){
            $remisiones = Remisione::where('cliente_id', $id)
                            ->whereBetween('fecha_creacion', [$inicio, $final])
                            ->orderBy('id','desc')
                            ->with('cliente')
                            ->get();
        }
        if($id != 0){
            $remisiones = Remisione::where('cliente_id', $id)->orderBy('id','desc')->with('cliente')->get();
        }

        return response()->json($remisiones);
    }

    public function por_fecha(){
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        $cliente_id = Input::get('cliente_id');

        if($cliente_id != 0){
            $remisiones = Remisione::where('cliente_id', $cliente_id)
                            ->whereBetween('fecha_creacion', [$inicio, $final])
                            ->orderBy('id','desc')
                            ->with('cliente')->get();
        }
        else{
            $remisiones = Remisione::whereBetween('fecha_creacion', [$inicio, $final])
                                ->orderBy('id','desc')
                                ->with('cliente')->get();
        }

        return response()->json($remisiones);
    }

    public function por_estado(){
        $estado = Input::get('estado');
        $remisiones = Remisione::where('estado', $estado)->with('cliente')->get();
        return response()->json($remisiones);
    }

    public function imprimirSalida($id){
        $remision = Remisione::whereId($id)->first();
        $data['remision'] = $remision;

        if($remision->estado == 'Iniciado'){
            $datos = Dato::where('remisione_id', $remision->id)->with('libro')->get();
            $data['datos'] = $datos;
            $data['total_salida'] = NumerosEnLetras::convertir($remision->total);
            $pdf = PDF::loadView('remision.nota', $data);    
        }         
        if($remision->estado == 'Terminado'){
            $devoluciones = Devolucione::where('remisione_id', $remision->id)->with('libro')->with('dato')->get();
            $data['devoluciones'] = $devoluciones;
            $data['total_final'] = NumerosEnLetras::convertir($remision->total_pagar);
            $pdf = PDF::loadView('remision.final', $data); 
        }
        
        return $pdf->download('remision.pdf');
    }

    public function imprimirCliente($cliente_id, $inicio, $final){
        $cliente = Cliente::whereId($cliente_id)->first();
        if($cliente_id != 0 && $final == '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $cliente->id)->with('datos')->get();
        }
        if($cliente_id == 0 && $final != '0000-00-00'){
            $remisiones = Remisione::whereBetween('fecha_creacion', [$inicio, $final])->with('datos')->get();
        }
        if($cliente_id != 0 && $final != '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $cliente->id)->whereBetween('fecha_creacion', [$inicio, $final])->with('datos')->get();
        }
        if($cliente_id == 0 && $final == '0000-00-00'){
            $remisiones = Remisione::with('cliente', 'datos')->get();
        }
        
        $data = $this->valores($remisiones, $inicio, $final, $cliente);

        $pdf = PDF::loadView('remision.reporte', $data);
        return $pdf->download('reporte.pdf');
    }

    public function imprimirEstado($estado){
        $remisiones = Remisione::where('estado', $estado)->with('cliente')->get();

        $data = $this->valores($remisiones, '0000-00-00', '0000-00-00', null);

        $pdf = PDF::loadView('remision.reporte', $data);
        return $pdf->download('reporte.pdf');
    }

    public function valores($remisiones, $inicio, $final, $cliente){
        $total_salida = 0;
        $total_devolucion = 0;
        $total_pagos = 0;
        $total_pagar = 0;

        foreach($remisiones as $r){
            $total_salida += $r->total;
            $total_devolucion += $r->total_devolucion;
            $total_pagos += $r->pagos;
            $total_pagar += $r->total_pagar;            
        }

        $data['remisiones'] = $remisiones;
        $data['total_salida'] = $total_salida;
        $data['total_devolucion'] = $total_devolucion;
        $data['total_pagos'] = $total_pagos;
        $data['total_pagar'] = $total_pagar;
        $data['fecha_inicio'] = $inicio;
        $data['fecha_final'] = $final;
        $data['cliente'] = $cliente;

        return $data;
    }

    public function nueva(){
        $remision = Remisione::all()->count() + 1;

        // try {
        //     \DB::beginTransaction();
        //     $this->comprobar($remision - 1);
        //     //Si una remision estab siendo creada pero no se guardo se recuperan los datos en estado Eliminado e Iniciado
        //     //Se deshacen los cambios de disminucion de piezas
        //     $eliminados = Dato::where('remision_id', $remision)->where('estado', 'Eliminado')->get();
        //     if($eliminados->count() > 0){
        //         foreach($eliminados as $eliminado){
        //             $libro = Libro::whereId($eliminado->libro_id)->first();
        //             $libro->update(['piezas' => $libro->piezas + $eliminado->unidades]);
        //         }
        //     }

        //     $datos = Dato::where('remision_id', $remision)->where('estado', 'Iniciado')->get();
        //     if($datos->count() > 0){
        //         foreach($datos as $dato){
        //             $libro = Libro::whereId($dato->libro_id)->first();
        //             $libro->update(['piezas' => $libro->piezas + $dato->unidades]);
        //         }
        //     }

        //     Dato::where('remision_id', $remision)->where('estado', 'Eliminado')->delete();
        //     Dato::where('remision_id', $remision)->where('estado', 'Iniciado')->delete();
        //     Cliente::where('estado', 'Iniciado')->delete();
        //     \DB::commit();

        // } catch (Exception $e) {
        //     \DB::rollBack();
        // }
        
        return response()->json(null, 200);
    }

    public function nueva_edicion(){
        $id = Input::get('id');
        $this->comprobar($id);
        return response()->json(null, 200);
    }

    public function comprobar($remision){
        //En caso de que alguna remision estaba siendo editada y se elimino un dato pero no se guardo, 
        //se cambia el estado de nuevo a Terminado
        Dato::where('remisione_id', $remision)
            ->where('estado', 'Eliminado')
            ->update(['estado' => 'Terminado']);
        //En caso de haber agregado uno nuevo, se recuperan las piezas
        $noguardados = Dato::where('remisione_id', $remision)->where('estado', 'Iniciado')->get();
        if($noguardados->count() > 0){
            foreach($noguardados as $noguardado){
                $libro = Libro::whereId($noguardado->libro_id)->first();
                $libro->update(['piezas' => $libro->piezas + $noguardado->unidades]);
            }
        }
        //Se eliminan dichos datos
        Dato::where('remisione_id', $remision)->where('estado', 'Iniciado')->delete();
    }

    public function por_estado_libros(){
        $estado = Input::get('estado');
        if($estado == 'Iniciado'){
            $remisiones = \DB::table('libros')
            ->join('datos', 'libros.id', '=', 'datos.libro_id')
            ->select(
                'ISBN', 
                'titulo', 
                \DB::raw('SUM(datos.unidades) as unidades'),
                \DB::raw('SUM(datos.total) as total'))
            ->groupBy('ISBN', 'titulo')
            ->get();
        }
        if($estado == 'Proceso'){
            $remisiones = \DB::table('libros')
            ->join('devoluciones', 'libros.id', '=', 'devoluciones.libro_id')
            ->select(
                'ISBN', 
                'titulo', 
                \DB::raw('SUM(devoluciones.unidades) as unidades_devolucion'),
                \DB::raw('SUM(devoluciones.total) as total_devolucion'),
                \DB::raw('SUM(devoluciones.unidades_resta) as unidades_final'), 
                \DB::raw('SUM(devoluciones.total_resta) as total_final'))
            ->groupBy('ISBN', 'titulo')
            ->get();
        }
        if($estado == 'Terminado'){
            $remisiones = \DB::table('libros')
            ->join('devoluciones', 'libros.id', '=', 'devoluciones.libro_id')
            ->select(
                'ISBN', 
                'titulo', 
                \DB::raw('SUM(devoluciones.unidades_resta) as unidades_final'), 
                \DB::raw('SUM(devoluciones.total_resta) as total_final'))
            ->groupBy('ISBN', 'titulo')
            ->get();
        }
        
        return response()->json($remisiones);
    }

    //Registrar vendidos
    public function registrar_vendidos(Request $request){
        $remision = Remisione::whereId($request->id)->first();
        $datos = Dato::where('remisione_id', $remision->id)->with('libro')->get();
        try {
            if(Vendido::where('remisione_id', $remision->id)->count() == 0){  
                \DB::beginTransaction();
                $remision->update(['estado' => 'Proceso']);
                $total_pagar = 0;
                foreach($datos as $d){
                    $total_pagar += $d->total;            
                }
                $remision->update(['total_pagar' => $total_pagar]);
                foreach($datos as $dato){
                    $vendido = Vendido::create([
                        'remisione_id' => $dato->remisione_id,
                        'dato_id'   => $dato->id,
                        'libro_id' => $dato->libro_id, 
                        'unidades_resta' => $dato->unidades,
                        'total_resta' => $dato->total,
                    ]);
                    $devolucion = Devolucione::create([
                        'remisione_id' => $dato->remisione_id,
                        'dato_id'   => $dato->id,
                        'libro_id' => $dato->libro_id,
                        'unidades_resta' => $dato->unidades,
                        'total_resta' => $dato->total,
                    ]);
                }
                \DB::commit();
            }
            
            $vendidos = Vendido::where('remisione_id', $remision->id)->with('libro')->with('dato')->get();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json(['vendidos' => $vendidos, 'remision' => $remision]);
    }

    public function cancelar_remision(Request $request){
        $remision = Remisione::whereId($request->id)->first();
        $datos = Dato::where('remisione_id', $remision->id)->get();
        try{
            \DB::beginTransaction();
            foreach($datos as $dato){
                $libro = Libro::whereId($dato->libro_id)->first();
                $libro->update(['piezas' => $libro->piezas + $dato->unidades]);
            }
            $remision->update(['estado' => 'Cancelado']);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
        
        return response()->json($remision);
    }

    public function obtener_vendidos(){
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
        return response()->json($datos);
    }

    //Función para obtener vendidos por fecha
    public function obtener_por_fecha(){
        $fecha = Input::get('fecha');
        $datos = \DB::table('vendidos')
                    ->join('libros', 'vendidos.libro_id', '=', 'libros.id')
                    ->whereDate('vendidos.created_at', $fecha)
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
        
        return response()->json($datos);
    }

    //Función para mostrar detalles
    public function detalles_vendidos(){
        $fecha = Input::get('fecha');
        $libro_id = Input::get('libro_id');

        if($fecha != null){
            $remisiones = \DB::table('vendidos')
                        ->join('remisiones', 'vendidos.remisione_id', '=', 'remisiones.id')
                        ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
                        ->whereDate('vendidos.created_at', $fecha)
                        ->where('vendidos.libro_id', $libro_id)
                        ->select(
                            'clientes.name as cliente', 
                            \DB::raw('SUM(vendidos.unidades) as unidades_vendidas'),
                            \DB::raw('SUM(vendidos.unidades_resta) as unidades_pendientes')
                        )
                        ->groupBy('clientes.name')
                        ->get();
        }
        else{
            $remisiones = \DB::table('vendidos')
                ->join('remisiones', 'vendidos.remisione_id', '=', 'remisiones.id')
                ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
                ->where('vendidos.libro_id', $libro_id)
                ->select(
                    'clientes.name as cliente', 
                    \DB::raw('SUM(vendidos.unidades) as unidades_vendidas'),
                    \DB::raw('SUM(vendidos.unidades_resta) as unidades_pendientes')
                )
                ->groupBy('clientes.name')
                ->get();

        }
        return response()->json($remisiones);
    }

    public function deposito_remision(Request $request){
        $remision = Remisione::whereId($request->remision_id)->first();
        
        $pagos = $remision->pagos + $request->pago;
        $total_pagar = $remision->total_pagar - $request->pago;
        try{
            \DB::beginTransaction();
            Deposito::create([
                'remisione_id' => $request->remision_id,
                'pago' => $request->pago
            ]);

            if($total_pagar == 0){
                Devolucione::where('remisione_id', $remision->id)->update([
                    'unidades_resta' => 0,
                    'total_resta' => 0
                ]);
                
                $vendidos = Vendido::where('remisione_id', $remision->id)->get();
                foreach($vendidos as $vendido){
                    $unidades = $vendido->unidades + $vendido->unidades_resta;
                    $total = $vendido->dato->costo_unitario * $unidades;
                    $vendido->update([
                        'unidades' => $unidades,
                        'total' => $total,
                        'unidades_resta' => 0,
                        'total_resta' => 0
                    ]);
                }

            }
            
            $remision->update([
                'pagos' => $pagos,
                'total_pagar' => $total_pagar
            ]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        
        return response()->json($remision);
    }

    // public function aplicar_descuento(Request $request){
    //     $remision = Remisione::whereId($request->id)->first();
    //     $total_pagar = $remision->total - (($remision->total * $request->descuento) / 100);
    //     $remision->update([
    //         'total_descuento' => $total_pagar,
    //         'descuento' => $request->descuento
    //     ]);
        
    //     return response()->json($remision);
    // }
}
