<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Repayment;
use App\Registro;
use App\Entrada;
use App\Libro;
use PDF;

class EntradaController extends Controller
{
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
        $fecha1 	= new Carbon(Input::get('fecha1'));
        $fecha1 	= $fecha1->format('Y-m-d 00:00:00');
        $fecha2 	= new Carbon(Input::get('fecha1'));
        $fecha2 	= $fecha2->format('Y-m-d 23:59:59');  

        $entradas = Entrada::whereBetween('created_at', [$fecha1, $fecha2])->get();
        return response()->json($entradas);
    }

    // MOSTRAR DETALLES DE UNA ENTRADA
    // Función utilizada en EditarEntradasComponent, EntradasComponent
    public function detalles_entrada(){
        $entrada_id = Input::get('entrada_id');
        // try {
        //     \DB::beginTransaction();
        //         Registro::where('entrada_id', $entrada_id)
        //         ->where('estado', 'Eliminado')
        //         ->update(['estado' => 'Terminado']);
        //     \DB::commit();
        
        // } catch (Exception $e) {
        //     \DB::rollBack();
        //     return response()->json($e->getMessage());
        // }
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

    // ACTUALIZAR DATOS DE ENTRADA
    // Función utilizada en EntradasComponent
    public function actualizar(Request $request){
        $entrada = Entrada::whereId($request->id)->first();
        try {
            \DB::beginTransaction();

            foreach($request->nuevos as $nuevo){
                Registro::create([
                    'entrada_id' => $entrada->id,
                    'libro_id'  => $nuevo['id'],
                    'unidades'  => $nuevo['unidades'],
                    'estado'    => 'Terminado'
                ]);
    
                $libro = Libro::whereId($nuevo['id'])->first();
                $libro->update(['piezas' => $libro->piezas + $nuevo['unidades']]);
            }

            $entrada->folio = $request->folio;
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

    // GUARDAR UNA NUEVA ENTRADA
    // Función utilizada en EntradasComponent
    public function store(Request $request){
        try {
            \DB::beginTransaction();

            $entrada = Entrada::create([
                'folio' => $request->folio,
                'editorial' => $request->editorial,
                'unidades' => $request->unidades,
            ]);
            $unidades = 0;
            foreach($request->items as $item){
                $registro = Registro::create([
                    'entrada_id' => $entrada->id,
                    'libro_id'  => $item['id'],
                    'unidades'  => $item['unidades'],
                    'estado'    => 'Terminado'
                ]);
    
                $libro = Libro::whereId($item['id'])->first();
                $libro->update(['piezas' => $libro->piezas + $item['unidades']]);
                $unidades += $item['unidades'];
            }
            $entrada->update(['unidades' => $unidades]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($entrada);
    }

    // IMPRIMIR REPORTE DE ENTRADA
    public function imprimirEntrada($id){
        $entrada = Entrada::whereId($id)->first();
        $data['entrada'] = $entrada;

        $registros = Registro::where('entrada_id', $entrada->id)->with('libro')->get();
        $data['entrada'] = $entrada;
        $data['registros'] = $registros;

        if(auth()->user()->role_id === 3){
            $pdf = PDF::loadView('inventario.reporte', $data); 
        }
        else{
            $pdf = PDF::loadView('inventario.entrada', $data); 
        }
        
        return $pdf->download('entrada.pdf');
    }

    // ELIMINAR REGISTRO DE ENTRADA (ELIMINADO DEL COMPONENTE)
    // Función utilizada en EntradasComponent
    public function eliminar(Request $request){
        try {
            \DB::beginTransaction();

            $registro = Registro::whereId($request->id)->update(['estado' => 'Eliminado']);

            \DB::commit();
            
            return response()->json($registro);
        
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }
    
    // VERIFICAR QUE EL REGISTRO ESTE EN ESTADO ELIMINADO (FUNCIÓN ELIMINADA DEL CONTROLADOR)
    public function concluir_registro($id){
        $registros = Registro::where('entrada_id', $id)->where('estado', 'Eliminado')->get();
        foreach($registros as $registro){
            $libro = Libro::whereId($registro->libro_id)->first();
            $libro->update(['piezas' => $libro->piezas - $registro->unidades]);
        }

        Registro::where('entrada_id', $id)->where('estado', 'Eliminado')->delete();
    }
}
