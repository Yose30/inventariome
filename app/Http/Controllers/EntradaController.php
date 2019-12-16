<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Exports\EntradasExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Repayment;
use App\Registro;
use App\Entrada;
use App\Libro;
use Excel;
use PDF;

class EntradaController extends Controller
{
    // GUARDAR UNA NUEVA ENTRADA
    // Función utilizada en EntradasComponent
    public function store(Request $request){
        try {
            \DB::beginTransaction();

            $entrada = Entrada::create([
                'folio' => strtoupper($request->folio),
                'editorial' => $request->editorial,
                'unidades' => $request->unidades,
            ]);
            $unidades = 0;
            foreach($request->items as $item){
                $unidades_base = $item['unidades'];
                $registro = Registro::create([
                    'entrada_id' => $entrada->id,
                    'libro_id'  => $item['id'],
                    'unidades'  => $unidades_base
                ]);
    
                $libro = Libro::whereId($item['id'])->first();
                $libro->update(['piezas' => $libro->piezas + $unidades_base]);
                $unidades += $unidades_base;
            }
            $entrada->update(['unidades' => $unidades]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($entrada);
    }
    
    // ACTUALIZAR DATOS DE ENTRADA
    // Función utilizada en EntradasComponent
    public function actualizar(Request $request){
        $entrada = Entrada::whereId($request->id)->first();
        try {
            \DB::beginTransaction();

            foreach($request->nuevos as $nuevo){
                $unidades_base = $nuevo['unidades'];
                Registro::create([
                    'entrada_id' => $entrada->id,
                    'libro_id'  => $nuevo['id'],
                    'unidades'  => $unidades_base
                ]);
    
                $libro = Libro::whereId($nuevo['id'])->first();
                $libro->update(['piezas' => $libro->piezas + $unidades_base]);
            }

            $entrada->folio = strtoupper($request->folio);
            $entrada->editorial = $request->editorial;
            $entrada->unidades = $request->unidades;
            $entrada->save();

            \DB::commit();

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($entrada);
    }

    // MOSTRAR ENTRADAS POR EDITORIAL
    // Función utilizada en EntradasComponent, EditarEntradasComponent, VendidosComponent
    public function mostrarEditoriales(){
        $editorial = Input::get('editorial');
        if($editorial == 'TODAS'){
            $entradas = Entrada::orderBy('id','desc')->get();
        }
        else{
            $entradas = Entrada::where('editorial','like','%'.$editorial.'%')->orderBy('id','desc')->get();
        }
        return response()->json($entradas);
    }

    // MOSTRAR ENTRADAS POR FECHA
    // Función utilizada en EditarEntradasComponent
    public function fecha_entradas(){
        $editorial 	= Input::get('editorial');
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        $fechas = $this->format_date($inicio, $final);
        $fecha1 = $fechas['inicio'];
        $fecha2 = $fechas['final'];

        if($editorial === null || $editorial == 'TODAS'){
            $entradas = Entrada::whereBetween('created_at', [$fecha1, $fecha2])->orderBy('id','desc')->get();
        } else {
            $entradas = Entrada::where('editorial', $editorial)
                        ->whereBetween('created_at', [$fecha1, $fecha2])
                        ->orderBy('id','desc')->get();
        }
        return response()->json($entradas);
    }

    // MOSTRAR DETALLES DE UNA ENTRADA
    // Función utilizada en EditarEntradasComponent, EntradasComponent
    public function detalles_entrada(){
        $entrada_id = Input::get('entrada_id');
        $entrada = Entrada::whereId($entrada_id)->with(['repayments', 'registros.libro'])->first();
        return response()->json(['entrada' => $entrada]);
    }

    // GUARDAR COSTOS DE LA ENTRADA
    // Función utilizada en EditarEntradasComponent
    public function actualizar_costos(Request $request){
        $total = 0;
        try {
            \DB::beginTransaction();
            foreach($request->items as $item){
                Registro::whereId($item['id'])->update([
                    'costo_unitario' => $item['costo_unitario'],
                    'total' => $item['total']
                ]);
                $total += $item['total'];
            }
            $entrada = Entrada::whereId($request->id)->first();
            $entrada->total = $total;
            $entrada->save();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($entrada);
    }

    // GUARDAR PAGO DE ENTRADA
    // Función utilizada en EditarEntradasComponent
    public function pago_entrada(Request $request){
        try {
            \DB::beginTransaction();
            $entrada = Entrada::whereId($request->entrada_id)->first();
            $repayment = Repayment::create([
                'entrada_id'    => $entrada->id,
                'pago'          => $request->pago
            ]);
            $entrada->update([
                'total_pagos' => $entrada->total_pagos + $request->pago
            ]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        
        return response()->json($entrada);
    }

    // BUSCAR ENTRADA POR FOLIO
    // Función utilizada en EntradasComponent
    public function buscarFolio(){
        $folio = Input::get('folio');
        $entrada = Entrada::where('folio', $folio)->first();
        return response()->json($entrada);
    }

    // IMPRIMIR REPORTE DE ENTRADA
    public function downloadEntrada($id){
        $entrada = Entrada::whereId($id)->with(['registros.libro', 'repayments'])->first();
        $data['entrada'] = $entrada;

        if(auth()->user()->role_id === 3){
            $pdf = PDF::loadView('download.pdf.entradas.entrada-unidades', $data); 
        }
        else{
            $pdf = PDF::loadView('download.pdf.entradas.entrada-costos', $data); 
        }
        
        return $pdf->download('entrada-'.$id.'.pdf');
    }

    // DESCARGAR TODAS LAS ENTRADAS
    public function downEntradas($inicio, $final, $editorial){
        $data['fecha'] = Carbon::now();
        $data['inicio'] = $inicio;
        $data['final'] = $final;

        if($final != '0000-00-00'){
            $fechas = $this->format_date($inicio, $final);
            $inicio = $fechas['inicio'];
            $final = $fechas['final'];
        }

        if($final === '0000-00-00' && $editorial === 'TODAS')
            $entradas = Entrada::orderBy('id','desc')->get(); 
        if($final !== '0000-00-00' && $editorial === 'TODAS')
            $entradas = Entrada::whereBetween('created_at', [$inicio, $final])->orderBy('id','desc')->get();
        if($final === '0000-00-00' && $editorial !== 'TODAS')
            $entradas = Entrada::where('editorial', $editorial)->orderBy('id','desc')->get();
        if($final !== '0000-00-00' && $editorial !== 'TODAS'){
            $entradas = Entrada::where('editorial', $editorial)
                        ->whereBetween('created_at', [$inicio, $final])
                        ->orderBy('id','desc')->get();
        }
        $totales = $this->acumular_totales($entradas);
        $data['total_unidades'] = $totales['total_unidades'];
        $data['total'] = $totales['total'];
        $data['total_pagos'] = $totales['total_pagos'];
        $data['total_pendiente'] = $totales['total_pendiente'];

        $data['editorial'] = $editorial;
        $data['entradas'] = $entradas;
        
        $pdf = PDF::loadView('download.pdf.entradas.reporte-gral', $data);
        return $pdf->download('reporte-entradas.pdf');
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

    public function acumular_totales($entradas){
        $total_unidades = 0;
        $total = 0;
        $total_pagos = 0;
        $total_pendiente = 0;
        foreach($entradas as $entrada){
            $total_unidades += $entrada->unidades;     
            $total += $entrada->total;
            $total_pagos += $entrada->total_pagos;
            $total_pendiente += $entrada->total - $entrada->total_pagos;

        }
        $totales = [
            'total_unidades' => $total_unidades,
            'total' => $total,
            'total_pagos' => $total_pagos,
            'total_pendiente' => $total_pendiente,
        ];
        return $totales;
    }

    // DESCARGAR REPORTE DE ENTRADAS EN EXCEL
    public function downEntradasEXC($inicio, $final, $editorial, $tipo){
        return Excel::download(new EntradasExport($inicio, $final, $editorial, $tipo), 'reporte-entradas.xlsx');
    }

    // ELIMINAR REGISTRO DE ENTRADA (ELIMINADO DEL COMPONENTE)
    // Función utilizada en EntradasComponent
    public function eliminar(Request $request){
        // try {
        //     \DB::beginTransaction();

        //     $registro = Registro::whereId($request->id)->update(['estado' => 'Eliminado']);

        //     \DB::commit();
            
        //     return response()->json($registro);
        
        // } catch (Exception $e) {
        //     \DB::rollBack();
        //     return response()->json($exception->getMessage());
		// }
    }
    
    // VERIFICAR QUE EL REGISTRO ESTE EN ESTADO ELIMINADO (FUNCIÓN ELIMINADA DEL CONTROLADOR)
    public function concluir_registro($id){
        // $registros = Registro::where('entrada_id', $id)->where('estado', 'Eliminado')->get();
        // foreach($registros as $registro){
        //     $libro = Libro::whereId($registro->libro_id)->first();
        //     $libro->update(['piezas' => $libro->piezas - $registro->unidades]);
        // }

        // Registro::where('entrada_id', $id)->where('estado', 'Eliminado')->delete();
    }
}
