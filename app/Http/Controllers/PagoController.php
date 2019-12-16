<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Devolucione;
use App\Remisione;
use App\Deposito;
use App\Vendido;
use App\Pago;
use App\Dato;

class PagoController extends Controller
{
    // MOSTRAR PAGOS
    // Función utilizada en DevolucionComponent, PagosComponent
    public function all_pagos(){
        $cliente_id = Input::get('cliente_id');
        $remisiones = Remisione::where('cliente_id', $cliente_id)
                    ->where('total_pagar', '>', 0)
                    ->where(function ($query) {
                        $query->where('estado', '=', 'Proceso')
                            ->orWhere('estado', '=', 'Terminado');
                    })->orderBy('id','desc')
                    ->with('cliente')->get();
        return response()->json($remisiones);
    }

    // MOSTRAR DATOS
    // Función utilizada en DevoluciónComponent y PagosComponent
    public function datos_vendidos(){
        $remision_id = Input::get('remision_id');
        $vendidos = Vendido::where('remisione_id', $remision_id)->with('libro')->with('pagos')->with('dato')->get();
        $depositos = Deposito::where('remisione_id', $remision_id)->get();
        return response()->json(['vendidos' => $vendidos, 'depositos' => $depositos]);
    } 

    // REGISTRAR PAGO DE REMISIÓN (POR UNIDADES)
    // Función utilizada en AdeudosComponent, DevoluciónAdeudosComponent, DevoluciónComponent, PagosComponent
    public function store(Request $request){
        try{
            \DB::beginTransaction();
            // Buscar remisión
            $remision = Remisione::whereId($request->id)->first();
            $entregado_por = $request->entregado_por;
            $pagos = 0;
            foreach($request->vendidos as $vendido){
                $unidades_base = $vendido['unidades_base'];
                $total_base = $vendido['total_base'];

                if($unidades_base != 0){
                    // Guardar pagos por libro
                    Pago::create([
                        'user_id' => auth()->user()->id,
                        'vendido_id' => $vendido['id'],
                        'unidades' => $unidades_base,
                        'pago' => $total_base,
                        'entregado_por' => $entregado_por
                    ]);

                    // Buscar el dato vendido
                    $d_vendido = Vendido::whereId($vendido['id'])->first();
                    $unidades = $d_vendido->unidades + $unidades_base;
                    $total = $d_vendido->total + $total_base;
                    $unidades_resta = $d_vendido->unidades_resta - $unidades_base;
                    $total_resta = $d_vendido->total_resta - $total_base;
                    
                    $d_vendido->update([
                        'unidades' => $unidades, 
                        'unidades_resta' => $unidades_resta,
                        'total' => $total,
                        'total_resta' => $total_resta
                    ]); 
                    
                    // Actualizar los datos de devolución
                    Devolucione::where('dato_id', $vendido['dato']['id'])->update([
                        'unidades_resta' => $unidades_resta,
                        'total_resta' => $total_resta
                    ]);
                }
                $pagos += $total_base;
            }
            
            $total_pagar = $remision->total_pagar - $pagos;
            $total_pagos = $remision->pagos + $pagos; 
            $remision->update([
                'pagos' => $total_pagos,
                'total_pagar'   => $total_pagar
            ]);
            if ((int) $total_pagar === 0) {
                if ($remision->depositos->count() > 0)
                    $this->restantes_to_cero($remision);
                $remision->update(['estado' => 'Terminado']);
            }
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
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
                'pago' => $request->pago,
                'entregado_por' => $request->entregado_por
            ]);
            
            $remision->update([
                'pagos' => $pagos,
                'total_pagar' => $total_pagar
            ]);

            if ((int) $total_pagar === 0){
                $this->restantes_to_cero($remision);
                $remision->update(['estado' => 'Terminado']);
            }
                
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        
        return response()->json($remision);
    }

    // ACTUALIZAR LAS UNIDADES RESTANTES DE LAS REMISIONES
    // SOLO SI EN LA REMISIÓN SE REALIZO UN DEPOSITO
    public function restantes_to_cero($remision) {
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
}
