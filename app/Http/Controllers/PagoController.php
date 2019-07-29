<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Devolucione;
use App\Remisione;
use App\Vendido;
use App\Pago;
use App\Dato;

class PagoController extends Controller
{
    public function store(Request $request){
        try{
            \DB::beginTransaction();
            $pagos = 0;
            foreach($request->vendidos as $vendido){
                $unidades = $vendido['unidades_base'];
                $costo_unitario = $vendido['dato']['costo_unitario'];
                $pago_total = $unidades * $costo_unitario;
                
                if($unidades != 0){
                    Pago::create([
                        'user_id' => auth()->user()->id,
                        'vendido_id' => $vendido['id'],
                        'unidades' => $unidades,
                        'pago' => $pago_total
                    ]);
                }

                $d_vendido = Vendido::whereId($vendido['id'])->first();
                $v_unidades = $d_vendido->unidades + $unidades;
                $unidades_resta = $d_vendido->unidades_resta - $unidades;
                $total = $v_unidades * $costo_unitario;
                $total_resta = $unidades_resta * $costo_unitario;
                $d_vendido->update([
                    'unidades' => $v_unidades, 
                    'unidades_resta' => $unidades_resta,
                    'total' => $total,
                    'total_resta' => $total_resta
                ]);
                
                Devolucione::where('dato_id', $vendido['dato']['id'])->update(['unidades_resta' => $unidades_resta]);
                $pagos += $total;
            }
            $remision = Remisione::whereId($request->id)->first();
            $total_pagar = $remision->total - ($pagos + $remision->total_devolucion);
            $remision->update([
                'pagos' => $pagos,
                'total_pagar'   => $total_pagar
            ]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
        }
        
        return response()->json($remision);
    }

    public function datos_vendidos(){
        $remision_id = Input::get('remision_id');
        $vendidos = Vendido::where('remision_id', $remision_id)->with('libro')->with('pagos')->with('dato')->get();
        return response()->json($vendidos);
    }

    // public function obtener_pagos(){
    //     $remision_id = Input::get('remision_id');
    //     $vendidos = Vendido::where('remision_id', $remision_id)->with('libro')->with('dato')->get();
    //     return response()->json($remision_id);
    // }
}
