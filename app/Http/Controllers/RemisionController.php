<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use NumerosEnLetras;
use App\Devolucione;
use Carbon\Carbon;
use App\Remisione;
use App\Donacione;
use App\Deposito;
use App\Vendido;
use App\Cliente;
use App\Libro;
use App\Fecha;
use App\Dato;
use App\Pago;
use PDF;

class RemisionController extends Controller
{

    // BUSCAR REMISIÓN POR NUMERO
    // Función utilizada en ListadoComponent y RemisionesComponent
    public function por_numero(){
        $num_remision = Input::get('num_remision');
        $remision = Remisione::whereId($num_remision)->with('cliente')->first();
        return response()->json(['remision' => $remision]);
    }

    // MOSTRAR REMISIONES POR CLIENTE
    // Función utilizada en ListadoComponent y RemisionesComponent
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
        if($id != 0)
            $remisiones = Remisione::where('cliente_id', $id)->orderBy('id','desc')->with('cliente')->get();

        return response()->json($remisiones);
    }

    // MOSTRAR REMISIONES POR ESTADO
    // Función utilizado en ListadoComponent
    public function por_estado(){
        $estado = Input::get('estado');
        if($estado == 'cancelado'){
            $remisiones = Remisione::where('estado',4)
                                    ->orderBy('id','desc')
                                    ->with('cliente:id,name')->get();
        }
        if($estado == 'no_entregado'){
            $remisiones = Remisione::where('estado',1)
                                    ->orderBy('id','desc')
                                    ->with('cliente:id,name')->get();
        }
        if($estado == 'entregado'){
            $remisiones = Remisione::where('estado',2)->where('total_pagar', '>', 0)
                                    ->orderBy('id','desc')
                                    ->with('cliente:id,name')->get();
        }
        if($estado == 'pagado'){
            $remisiones = Remisione::where('total_pagar', '=', 0)->where('pagos', '>', 0)
                                    ->orderBy('id','desc')
                                    ->with('cliente:id,name')->get();
        }
        return response()->json($remisiones);
    }

    // MOSTRAR REMISIONES POR FECHAS
    // Función utilizada en ListadoComponent
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

    // IMPRIMIR PDF CON LOS DATOS DE LAS REMISIONES POR CLIENTE
    // Función utilizada en ListadoComponent
    public function imprimirCliente($cliente_id, $inicio, $final){
        $cliente = Cliente::whereId($cliente_id)->select('id', 'name')->first();
        if($inicio != '0000-00-00' && $final != '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $cliente->id)
                            ->whereBetween('fecha_creacion', [$inicio, $final])
                            ->whereNotIn('estado', ['Cancelado'])
                            ->with('datos.libro')
                            ->orderBy('id','desc')
                            ->get();
        }
        else { 
            $remisiones = Remisione::where('cliente_id', $cliente->id)
                            ->whereNotIn('estado', ['Cancelado'])
                            ->with('datos.libro')
                            ->orderBy('id','desc')
                            ->get();
        }
    
        $data['cliente'] = $cliente->name;
        $data['remisiones'] = $remisiones;
        $pdf = PDF::loadView('remision.reporte', $data);
        return $pdf->download('reporte.pdf');
    }
    
    // MOSTRAR DETALLES DE REMISIÓN
    // Función utilizada en ListadoComponent, DevoluciónComponent, RemisionesComponent
    public function show(){
        $numero = Input::get('numero');
        $remision = Remisione::whereId($numero)
                    ->with(['datos.libro', 'fechas.libro','donaciones.libro','devoluciones.libro','devoluciones.dato', 'depositos'])
                    ->first(); 
        $vendidos = Vendido::where('remisione_id', $remision->id)->with('libro', 'dato', 'pagos')->get();
        return response()->json(['remision' => $remision, 'vendidos' => $vendidos]);
    }

    // CANCELAR REMISIÓN
    // Función utilizada en ListadoComponent
    public function cancelar_remision(Request $request){
        $remision = Remisione::whereId($request->id)->first();
        try{
            \DB::beginTransaction();
            foreach($remision->datos as $dato){
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

    //OBTENER VENDIDOS POR FECHA
    // Función utilizada en VendidosComponent
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

    // MOSTRAR DETALLES DE VENDIDOS
    // Función utilizada en VendidosComponent
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

    // GUARDAR REMISION
    // Función utilizada en RemisionComponent
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
    
    // GUARDAR DEPOSITO DE REMISIÓN
    // Función utilizada en PagosComponent
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

    // MARCAR COMO ENTREGADA UNA REMISIÓN
    // Función utilizada en RemisionesComponent
    public function registrar_vendidos(Request $request){
        $remision = Remisione::whereId($request->id)->with('datos.libro')->first();
        try {
            if(Vendido::where('remisione_id', $remision->id)->count() == 0){  
                \DB::beginTransaction();
                $remision->update(['estado' => 'Proceso']);
                $total_pagar = 0;
                foreach($remision->datos as $d){
                    $total_pagar += $d->total;            
                }
                $remision->update(['total_pagar' => $total_pagar]);
                foreach($remision->datos as $dato){
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
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json(['remision' => $remision]);
    }
    // OBTENER TODAS LAS REMISIONES DE LOS CLIENTES
    // Función utilizada en ListadoComponent
    public function todos(){
        $remisiones = Remisione::with('cliente:id,name')->orderBy('id','desc')->get();
        return response()->json($remisiones);
    } 

    // DESCARGAR REMISIÓN
    public function imprimirSalida($id){ 
        $remision = Remisione::whereId($id)->with('datos.libro')->first();
        $data['remision'] = $remision;

        $datos = $remision->datos;
        $data['datos'] = $datos;
        $data['total_salida'] = NumerosEnLetras::convertir($remision->total);
        $pdf = PDF::loadView('remision.nota', $data); 
        
        return $pdf->download('remision.pdf');
    }

    // OBTENER DETALLES DE LIBROS VENDIDOS
    // Función utilizada en VendidosComponent
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

    // CHECAR
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
            if($r->estado != 'Iniciado' && $r->estado != 'Cancelado'){
                $total_salida += $r->total;
                $total_devolucion += $r->total_devolucion;
                $total_pagos += $r->pagos;
                $total_pagar += $r->total_pagar;
            }            
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
}
