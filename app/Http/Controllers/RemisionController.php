<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Exports\RemisionesClientesExport;
use App\Exports\ClientesDetalladoExport;
use App\Exports\RemisionesEstadoExport;
use App\Exports\EstadoDetalladoExport;
use App\Exports\RemisionesFechasExport;
use App\Exports\FechasDetalladoExport;
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
use Excel;
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
        if($final && $final != '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $id)
                        ->whereBetween('fecha_creacion', [$inicio, $final])
                        ->orderBy('id','desc')
                        ->with('cliente')
                        ->get();
        } else {
            $remisiones = Remisione::where('cliente_id', $id)->orderBy('id','desc')->with('cliente')->get();
        }

        return response()->json($remisiones);
    }

    // MOSTRAR REMISIONES POR ESTADO
    // Función utilizado en ListadoComponent
    public function por_estado(){
        $estado = Input::get('estado');
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        $cliente_id = Input::get('cliente_id');

        if($estado === 'cancelado'){
            if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(4, $inicio, $final); }
            else { $remisiones = $this->estadoS_SF(4, $cliente_id); }
        }
        if($estado === 'no_entregado'){
            if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(1, $inicio, $final); }
            else { $remisiones = $this->estadoS_SF(1, $cliente_id); }
        }
        if($estado === 'entregado'){
            if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(2, $inicio, $final); }
            else { $remisiones = $this->estadoS_SF(2, $cliente_id); }
        }
        if($estado === 'pagado'){
            if($final != '0000-00-00'){ $remisiones = $this->pagado_CF($inicio, $final); }
            else { $remisiones = $this->pagado_SF($cliente_id); }
        }
        return response()->json($remisiones);
    }

    // IMPRIMIR REPORTE GENERAL Y DETALLADO
    public function imprimirEstado($estado, $cliente_id, $inicio, $final){
        if($estado === 'cancelado'){
            if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(4, $inicio, $final); }
            else { 
                if($cliente_id === 'null'){ $remisiones = $this->estadoS_SF(4, null); }
                else { $remisiones = $this->estadoS_SF(4, $cliente_id); } 
            }
        }
        if($estado === 'no_entregado'){
            if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(1, $inicio, $final); }
            else { 
                if($cliente_id === 'null'){ $remisiones = $this->estadoS_SF(1, null); }
                else { $remisiones = $this->estadoS_SF(1, $cliente_id); }
            }
        }
        if($estado === 'entregado'){
            if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(2, $inicio, $final); }
            else {
                if($cliente_id === 'null'){ $remisiones = $this->estadoS_SF(2, null); }
                else { $remisiones = $this->estadoS_SF(2, $cliente_id); }
            }
        }
        if($estado === 'pagado'){
            if($final != '0000-00-00'){ $remisiones = $this->pagado_CF($inicio, $final); }
            else {
                if($cliente_id === 'null'){ $remisiones = $this->pagado_SF(null);  }
                else { $remisiones = $this->pagado_SF($cliente_id);  }
                
            }
        }

        // $dif = $inicio->diff($final);
        // if((int)$tipo === 1){
        //     
        // } else {
        //     if($dif->days < 61){
        //         if($estado == 'cancelado'){
        //             $remisiones = Remisione::where('estado','Cancelado')
        //                                     ->whereBetween('fecha_creacion', [$inicio, $final])
        //                                     ->orderBy('id','desc')
        //                                     ->with('cliente:id,name')
        //                                     ->with('datos.libro')
        //                                     ->get();
        //         }
        //         if($estado == 'no_entregado'){
        //             $remisiones = Remisione::where('estado','iniciado')
        //                                     ->whereBetween('fecha_creacion', [$inicio, $final])
        //                                     ->orderBy('id','desc')
        //                                     ->with('cliente:id,name')
        //                                     ->with('datos.libro')
        //                                     ->get();
        //         }
        //         if($estado == 'entregado'){
        //             $remisiones = Remisione::where('estado','proceso')->where('total_pagar', '>', 0)
        //                                     ->whereBetween('fecha_creacion', [$inicio, $final])
        //                                     ->orderBy('id','desc')
        //                                     ->with('cliente:id,name')
        //                                     ->with('datos.libro')
        //                                     ->get();
        //         }
        //         if($estado == 'pagado'){
        //             $remisiones = Remisione::where('total_pagar', '=', 0)
        //                                     ->where(function ($query) {
        //                                         $query->where('pagos', '>', 0)
        //                                                 ->orWhere('total_devolucion', '>', 0);
        //                                     })
        //                                     ->whereBetween('fecha_creacion', [$inicio, $final])
        //                                     ->orderBy('id','desc')
        //                                     ->with('cliente:id,name')
        //                                     ->with('datos.libro')
        //                                     ->get();
        //         }
                $valores = $this->totales($remisiones);
                $data['remisiones'] = $remisiones;
                $data['estado'] = $estado;
                $data['inicio'] = $inicio;
                $data['final'] = $final;
                $data['fecha'] = $valores['fecha'];
                $data['totales'] = $valores['totales'];
                $pdf = PDF::loadView('remision.pdf.reporte-estado-gral', $data);
                return $pdf->download('reporte-estado-gral.pdf');
        //     } else {
        //         return back()->with('status', 'El rango de fecha debe ser igual o menor a dos meses.');
        //     }
        // }
    }

    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO CON FECHA
    public function estadoS_CF($estado, $inicio, $final){
        if ($estado == 1 || $estado == 4) {
            return Remisione::where('estado',$estado)
                ->whereBetween('fecha_creacion', [$inicio, $final])
                ->orderBy('cliente_id','asc')
                ->with('cliente:id,name')->get();
        }
        if ($estado == 2){
            return Remisione::where('estado',$estado)->where('total_pagar', '>', 0)
                ->whereBetween('fecha_creacion', [$inicio, $final])
                ->orderBy('cliente_id','asc')
                ->with('cliente:id,name')->get();
        }
    }
    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO SIN FECHA
    public function estadoS_SF($estado, $cliente_id){
        if ($estado == 1 || $estado == 4) {
            if($cliente_id === null){
                return Remisione::where('estado',$estado)->orderBy('cliente_id','asc')->with('cliente:id,name')->get();
            } else {
                return Remisione::where('estado',$estado)
                            ->where('cliente_id', $cliente_id)
                            ->orderBy('cliente_id','asc')
                            ->with('cliente:id,name')->get();
            }
        }
        if ($estado == 2){
            if($cliente_id === null){
                return Remisione::where('estado',$estado)->where('total_pagar', '>', 0)
                    ->orderBy('cliente_id','asc')
                    ->with('cliente:id,name')->get();
            } else {
                return Remisione::where('estado',$estado)
                    ->where('cliente_id', $cliente_id)
                    ->where('total_pagar', '>', 0)
                    ->orderBy('cliente_id','asc')
                    ->with('cliente:id,name')->get();
            }
        }
    }

    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO (PAGADO) CON FECHA
    public function pagado_CF($inicio, $final){
        return Remisione::where('total_pagar', '=', 0)
                        ->where(function ($query) {
                            $query->where('pagos', '>', 0)
                                    ->orWhere('total_devolucion', '>', 0);
                        })
                        ->whereBetween('fecha_creacion', [$inicio, $final])
                        ->orderBy('cliente_id','asc')
                        ->with('cliente:id,name')->get();
    }

    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO (PAGADO) SIN FECHA
    public function pagado_SF($cliente_id){
        if($cliente_id === null){
            return Remisione::where('total_pagar', '=', 0)
                        ->where(function ($query) {
                            $query->where('pagos', '>', 0)
                                    ->orWhere('total_devolucion', '>', 0);
                        })
                        ->orderBy('cliente_id','asc')
                        ->with('cliente:id,name')->get();
        } else {
            return Remisione::where('cliente_id', $cliente_id)
                        ->where('total_pagar', '=', 0)
                        ->where(function ($query) {
                            $query->where('pagos', '>', 0)
                                    ->orWhere('total_devolucion', '>', 0);
                        })
                        ->orderBy('cliente_id','asc')
                        ->with('cliente:id,name')->get();
        }
    }

    // MOSTRAR REMISIONES POR FECHAS
    // Función utilizada en ListadoComponent
    public function por_fecha(){
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        $remisiones = $this->remisiones_PF($inicio, $final);
        return response()->json($remisiones);
    }

    // RETORNAR LA BUSQUEDA DE REMISIONES POR FECHA
    public function remisiones_PF($inicio, $final){
        return Remisione::whereBetween('fecha_creacion', [$inicio, $final])
                            ->orderBy('cliente_id','asc')
                            ->with('cliente')->get();
    }

    // IMPRIMIR PDF DE REMISIONES POR FECHA
    // Función utilizada en ListadoComponent
    public function imprimirFecha($inicio, $final){
        // $remisiones =\DB::table('remisiones')
        // ->whereBetween('fecha_creacion', [$inicio, $final])
        // ->whereNotIn('remisiones.estado', ['Iniciado', 'Cancelado'])
        // ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
        // ->select('remisiones.id', 'fecha_creacion', 'clientes.name as cliente', 'total', 'pagos', 'total_devolucion', 'total_donacion', 'total_pagar')
        // ->orderBy('clientes.name','asc')
        // ->get();
        $remisiones = $this->remisiones_PF($inicio, $final);

        $datos = \DB::table('remisiones')
        ->whereBetween('fecha_creacion', [$inicio, $final])
        ->whereNotIn('remisiones.estado', ['Iniciado', 'Cancelado'])
        ->join('clientes', 'remisiones.cliente_id', '=', 'clientes.id')
        ->select('clientes.name as nombre', 
            \DB::raw('SUM(total) as total'),
            \DB::raw('SUM(pagos) as pagos'),
            \DB::raw('SUM(total_devolucion) as total_devolucion'),
            \DB::raw('SUM(total_donacion) as total_donacion'),
            \DB::raw('SUM(total_pagar) as total_pagar')
        )->groupBy('clientes.name')
        ->orderBy('clientes.id','asc')
        ->get();

        $valores = $this->totales($remisiones);
        $data['fecha'] = $valores['fecha'];
        $data['inicio'] = $inicio;
        $data['final'] = $final;
        $data['remisiones'] = $remisiones;
        $data['totales'] = $valores['totales'];
        $data['datos'] = $datos;
        $pdf = PDF::loadView('remision.pdf.reporte-fecha-gral', $data);
        return $pdf->download('reporte-fecha-gral.pdf');
    }

    
    // IMPRIMIR PDF CON LOS DATOS DE LAS REMISIONES POR CLIENTE
    // Función utilizada en ListadoComponent
    public function imprimirCliente($cliente_id, $inicio, $final){
        if($inicio != '0000-00-00' && $final != '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $cliente_id)
                            ->whereBetween('fecha_creacion', [$inicio, $final])
                            ->whereNotIn('estado', ['Iniciado', 'Cancelado'])
                            ->orderBy('id','desc')
                            ->get();
        }
        else {
            $remisiones = Remisione::where('cliente_id', $cliente_id)
                    ->whereNotIn('estado', ['Iniciado', 'Cancelado'])
                    ->orderBy('id','desc')
                    ->get();
        }
        $valores = $this->totales($remisiones);
        $data['fecha'] = $valores['fecha'];
        $data['inicio'] = $inicio;
        $data['final'] = $final;
        $data['remisiones'] = $remisiones;
        $data['totales'] = $valores['totales'];
        $pdf = PDF::loadView('remision.pdf.reporte-cliente-gral', $data);
        return $pdf->download('reporte-cliente-gral.pdf');
    }

    // OBTENER TOTALES DE LAS REMISIONES
    public function totales($remisiones){
        $total_salida = 0;
        $total_pagos = 0;
        $total_devolucion = 0;
        $total_donacion = 0;
        $total_pagar = 0;

        foreach($remisiones as $r){
            $total_salida += $r->total;
            $total_pagos += $r->pagos;
            $total_devolucion += $r->total_devolucion;
            $total_donacion += $r->total_donacion;
            $total_pagar += $r->total_pagar;            
        }
        $datos = [
            'fecha' => $fecha = Carbon::now(),
            'totales' => [
                'total_salida' => $total_salida,
                'total_pagos' => $total_pagos,
                'total_devolucion' => $total_devolucion,
                'total_donacion' => $total_donacion,
                'total_pagar' => $total_pagar
            ]
        ];
        return $datos;
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
            if(Vendido::where('remisione_id', $remision->id)->count() === 0){  
                \DB::beginTransaction();
                $remision->update(['estado' => 'Proceso']);
                // $total_pagar = 0;
                // foreach($remision->datos as $d){
                //     $total_pagar += $d->total;            
                // }
                $total_pagar = $remision->total;
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
                        'fecha_devolucion' => Carbon::now()->format('Y-m-d')
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

    // DESCARGAR REPORTE DE CLIENTE EN EXCEL
    public function imprimirClienteEXC($cliente_id, $inicio, $final){
        return Excel::download(new RemisionesClientesExport($cliente_id, $inicio, $final), 'reporte-cliente-gral.xlsx');
    }

    // DESCARGAR REPORTE DE CLIENTE DETALLADO EN EXCEL
    public function imprimirClienteDet($cliente_id, $inicio, $final){
        return Excel::download(new ClientesDetalladoExport($cliente_id, $inicio, $final), 'reporte-cliente-detallado.xlsx');
    }

    // DESCARGAR REPORTES POR ESTADO EN EXCEL
    public function imprimirEstadoEXC($estado, $cliente_id, $inicio, $final){
        return Excel::download(new RemisionesEstadoExport($estado, $cliente_id, $inicio, $final), 'reporte-estado-gral.xlsx');
    }

    // DESCARGAR REPORTES POR ESTADO DETALLADO EN EXCEL
    public function imprimirEstadoDet($estado, $cliente_id, $inicio, $final){
        return Excel::download(new EstadoDetalladoExport($estado, $cliente_id, $inicio, $final), 'reporte-estado-detallado.xlsx');
    }

    // DESCARGAR REPORTE POR FECHAS PDF
    public function imprimirFechaEXC($inicio, $final){
        return Excel::download(new RemisionesFechasExport($inicio, $final), 'reporte-fecha-gral.xlsx');
    }

    // DESCARGAR REPORTE POR FECHAS DETALLADO EXCEL
    public function imprimirFechaDet($inicio, $final){
        return Excel::download(new FechasDetalladoExport($inicio, $final), 'reporte-fecha-detallado.xlsx');
    }
}
