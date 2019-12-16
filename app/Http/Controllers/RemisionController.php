<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use App\Exports\RemisionesExport;
use App\Exports\RemisionExport;
use App\Exports\RemisionesGExport;
use Illuminate\Http\Request;
use NumerosEnLetras;
use App\Devolucione;
use App\Comentario;
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
    // --- BUSQUEDAS ---
    // BUSCAR REMISIÓN POR NUMERO
    // Función utilizada en ListadoComponent y RemisionesComponent
    public function por_numero(){
        $num_remision = Input::get('num_remision');
        $remision = Remisione::whereId($num_remision)->withCount('depositos')->with('cliente')->first();
        return response()->json(['remision' => $remision]);
    }

    // MOSTRAR REMISIONES POR CLIENTE
    // Función utilizada en ListadoComponent y RemisionesComponent
    public function buscar_por_cliente(){
        $id = Input::get('id');
        // $inicio = Input::get('inicio');
        // $final = Input::get('final');
        // if($final && $final != '0000-00-00'){
        //     $remisiones = Remisione::where('cliente_id', $id)
        //                 ->whereBetween('fecha_creacion', [$inicio, $final])
        //                 ->withCount('depositos')
        //                 ->orderBy('id','desc')
        //                 ->with('cliente')
        //                 ->get();
        // } else {
            $remisiones = Remisione::where('cliente_id', $id)
                        ->withCount('depositos')
                        ->orderBy('id','desc')->with('cliente')->get();
        // }

        return response()->json($remisiones);
    }

    // MOSTRAR REMISIONES POR ESTADO
    // Función utilizado en ListadoComponent
    public function buscar_por_estado(){
        $estado = Input::get('estado');
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        $cliente_id = Input::get('cliente_id');

        if($estado === 'cancelado'){
            // if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(4, $inicio, $final); }
            // else {  }
            $remisiones = $this->estadoS_SF(4, $cliente_id);
        }
        if($estado === 'no_entregado'){
            // if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(1, $inicio, $final); }
            // else {  }
            $remisiones = $this->estadoS_SF(1, $cliente_id);
        }
        if($estado === 'entregado'){
            // if($final != '0000-00-00'){ $remisiones = $this->estadoS_CF(2, $inicio, $final); }
            // else {  }
            $remisiones = $this->estadoS_SF(2, $cliente_id);
        }
        if($estado === 'pagado'){
            // if($final != '0000-00-00'){ $remisiones = $this->pagado_CF($inicio, $final); }
            // else {  }
            $remisiones = $this->pagado_SF($cliente_id);
        }
        return response()->json($remisiones);
    }

    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO CON FECHA
    public function estadoS_CF($estado, $inicio, $final){
        if ($estado == 1 || $estado == 4) {
            // REMISIONES INICIADAS Y CANCELADAS
            return Remisione::where('estado',$estado)
                ->whereBetween('fecha_creacion', [$inicio, $final])
                ->orderBy('cliente_id','asc')
                ->with('cliente:id,name')->get();
        }
        if ($estado == 2){
            // REMISIONES EN PROCESO
            return Remisione::where('total_pagar', '>', 0)
                ->whereBetween('fecha_creacion', [$inicio, $final])
                ->withCount('depositos')
                ->orderBy('cliente_id','asc')
                ->with('cliente:id,name')->get();
        }
    }
    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO SIN FECHA
    public function estadoS_SF($estado, $cliente_id){
        if ($estado == 1 || $estado == 4) {
            // REMISIONES INICIADAS Y CANCELADAS
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
            // REMISIONES EN PROCESO
            if($cliente_id === null){
                return Remisione::where('total_pagar', '>', 0)
                    ->withCount('depositos')
                    ->orderBy('cliente_id','asc')
                    ->with('cliente:id,name')->get();
            } else {
                return Remisione::where('cliente_id', $cliente_id)
                    ->where('total_pagar', '>', 0)
                    ->withCount('depositos')
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
                        ->withCount('depositos')
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
                        ->withCount('depositos')
                        ->orderBy('cliente_id','asc')
                        ->with('cliente:id,name')->get();
        } else {
            return Remisione::where('cliente_id', $cliente_id)
                        ->where('total_pagar', '=', 0)
                        ->where(function ($query) {
                            $query->where('pagos', '>', 0)
                                    ->orWhere('total_devolucion', '>', 0);
                        })
                        ->withCount('depositos')
                        ->orderBy('cliente_id','asc')
                        ->with('cliente:id,name')->get();
        }
    }

    // MOSTRAR REMISIONES POR FECHAS
    // Función utilizada en ListadoComponent
    public function buscar_por_fecha(){
        $cliente_id = Input::get('cliente_id');
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        if($cliente_id === null){
            $remisiones = Remisione::whereBetween('fecha_creacion', [$inicio, $final])
                ->orderBy('cliente_id','asc')
                ->withCount('depositos')
                ->with('cliente')->get(); 
        } else{
            $remisiones = Remisione::where('cliente_id', $cliente_id)
                        ->whereBetween('fecha_creacion', [$inicio, $final])
                        ->withCount('depositos')
                        ->orderBy('id','desc')
                        ->with('cliente')
                        ->get();
        }
        return response()->json($remisiones);
    }

    // --- DESCARGAR ---
    // DESCARGAR REPORTE GENERAL Y DETALLADO
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

        $valores = $this->totales($remisiones);
        $data['remisiones'] = $remisiones;
        $data['estado'] = $estado;
        $data['inicio'] = $inicio;
        $data['final'] = $final;
        $data['fecha'] = $valores['fecha'];
        $data['totales'] = $valores['totales'];
        $pdf = PDF::loadView('download.pdf.remisiones.reporte-estado-gral', $data);
        return $pdf->download('reporte-estado-gral.pdf');
    }

    // DESCARGAR PDF DE REMISIONES POR FECHA
    // Función utilizada en ListadoComponent
    public function imprimirFecha($inicio, $final){
        $remisiones = Remisione::whereBetween('fecha_creacion', [$inicio, $final])
                ->whereNotIn('estado', ['Iniciado', 'Cancelado'])
                ->orderBy('cliente_id','asc')
                ->with('cliente')->get();

        $valores = $this->totales($remisiones);
        $data['fecha'] = $valores['fecha'];
        $data['inicio'] = $inicio;
        $data['final'] = $final;
        $data['remisiones'] = $remisiones;
        $data['totales'] = $valores['totales'];
        $pdf = PDF::loadView('download.pdf.remisiones.reporte-fecha-gral', $data);
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
        $pdf = PDF::loadView('download.pdf.remisiones.reporte-cliente-gral', $data);
        return $pdf->download('reporte-cliente-gral.pdf');
    }

    public function down_gral_excel($cliente_id, $inicio, $final, $estado){
        return Excel::download(new RemisionesGExport($cliente_id, $inicio, $final, $estado), 'reporte-remisiones-gral.xlsx');
    }

    public function down_remisiones_excel($cliente_id, $inicio, $final, $estado){
        return Excel::download(new RemisionesExport($cliente_id, $inicio, $final, $estado), 'reporte-remisiones-detallado.xlsx');
    }
    
    public function down_remisiones_pdf($cliente_id, $inicio, $final, $estado){
        if($cliente_id === 'null' && $inicio === '0000-00-00' && $final === '0000-00-00' && $estado === 'null'){
            $remisiones = Remisione::with(['cliente:id,name'])->with('datos.libro')
                    ->orderBy('id','desc')
                    ->get();
        }
        if($cliente_id !== 'null' && $inicio === '0000-00-00' && $final === '0000-00-00' && $estado === 'null'){
            $remisiones = Remisione::where('cliente_id', $cliente_id)
                    ->orderBy('id','desc')
                    ->get();
        }
        if($cliente_id !== 'null' && $inicio != '0000-00-00' && $final != '0000-00-00' && $estado === 'null'){
            $remisiones = Remisione::where('cliente_id', $cliente_id)
                            ->whereBetween('fecha_creacion', [$inicio, $final])
                            ->orderBy('id','desc')
                            ->get();
        }
        if($cliente_id === 'null' && $inicio != '0000-00-00' && $final != '0000-00-00' && $estado === 'null'){
            $remisiones = Remisione::whereBetween('fecha_creacion', [$inicio, $final])
                            ->orderBy('cliente_id','asc')
                            ->with('cliente')->get();
        }
        if($estado !== 'null'){
            if($estado === 'cancelado'){
                if($cliente_id === 'null'){ $remisiones = $this->estadoS_SF(4, null); }
                else { $remisiones = $this->estadoS_SF(4, $cliente_id); } 
            }
            if($estado === 'no_entregado'){
                if($cliente_id === 'null'){ $remisiones = $this->estadoS_SF(1, null); }
                else { $remisiones = $this->estadoS_SF(1, $cliente_id); }
            }
            if($estado === 'entregado'){
                if($cliente_id === 'null'){ $remisiones = $this->estadoS_SF(2, null); }
                else { $remisiones = $this->estadoS_SF(2, $cliente_id); }
            }
            if($estado === 'pagado'){
                if($cliente_id === 'null'){ $remisiones = $this->pagado_SF(null);  }
                else { $remisiones = $this->pagado_SF($cliente_id);  }
            }
        }

        $valores = $this->totales($remisiones);
        $data['fecha'] = $valores['fecha'];
        $data['inicio'] = $inicio;
        $data['final'] = $final;
        $data['estado'] = $estado;
        $data['remisiones'] = $remisiones;
        $data['totales'] = $valores['totales'];
        $pdf = PDF::loadView('download.pdf.remisiones.reporte-remisiones-gral', $data);
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
            if($r->estado === 'Proceso' || $r->estado === 'Terminado'){
                $total_salida += $r->total;
                $total_pagos += $r->pagos;
                $total_devolucion += $r->total_devolucion;
                $total_donacion += $r->total_donacion;
                $total_pagar += $r->total_pagar;  
            }          
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
                    ->with([
                        'datos.libro',
                        'devoluciones.libro',
                        'devoluciones.dato',
                        'fechas.libro',
                        'depositos',
                        'comentarios.user'
                    ])->withCount('depositos')->first(); 
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
                    'total'     => $registro['total']
                ]);
    
                $libro = Libro::whereId($dato->libro_id)->first();
                $libro->update(['piezas' => $libro->piezas - $dato->unidades]);
            }
            
            $get_remision = Remisione::whereId($remision->id)
                            ->with(['cliente:id,name'])
                            ->first();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($get_remision);
    }

    // MARCAR COMO ENTREGADA UNA REMISIÓN
    // Función utilizada en RemisionesComponent
    public function registrar_vendidos(Request $request){
        $remision = Remisione::whereId($request->remision)->with('datos.libro')->first();
        try {
            if(Vendido::where('remisione_id', $remision->id)->count() === 0){  
                \DB::beginTransaction();
                $remision->update([
                    'estado' => 'Proceso',
                    'responsable' => $request->responsable
                ]);
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
        $data['fecha'] = Carbon::now();
        $data['total_salida'] = NumerosEnLetras::convertir($remision->total);
        $pdf = PDF::loadView('download.pdf.remisiones.nota', $data); 
        
        return $pdf->download('remision-'.$id.'.pdf');
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

    // GUARDAR COMENTARIO DE LA REMISIÓN
    public function guardar_comentario(Request $request){
        try {
            \DB::beginTransaction();
            $comentario = Comentario::create([
                'remisione_id' => $request->remision_id, 
                'user_id' => auth()->user()->id,
                'comentario' => $request->comentario
            ]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        $data = Comentario::whereId($comentario->id)->with('user')->first();
        return response()->json($data);
    }

    public function download_remision($id){
        return Excel::download(new RemisionExport($id), 'remision-'.$id.'.xlsx');
    }
}
