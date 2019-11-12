<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Devolucione;
use App\Remisione;
use App\Donacione;
use App\Vendido;

class DonacioneController extends Controller
{
    // GUARDAR UNA DONACIÓN
    // Función utilizada en DevoluciónController
    public function store(Request $request) {
        try{
            \DB::beginTransaction();
            // Buscar remisión
            $remision = Remisione::whereId($request->id)->first();
            $todo = 0;
            
            foreach($request->vendidos as $vendido){
                $unidades_base = $vendido['unidades_base'];
                $total_base = $vendido['total_base'];
                $libro_id = $vendido['libro_id'];
    
                if($unidades_base != 0){
                    // Crear registro de donación
                    Donacione::create([
                        'remisione_id' => $remision->id,
                        'libro_id' => $libro_id,
                        'unidades' => $unidades_base
                    ]);
                    // Buscar dato vendido
                    $v = Vendido::whereId($vendido['id'])->first();
                    
                    $unidades_resta = $v->unidades_resta - $unidades_base;
                    $total_resta = $v->total_resta - $total_base;
                    // Actualizar registro de vendido
                    $v->update([
                        'unidades_resta' => $unidades_resta,
                        'total_resta' => $total_resta
                    ]);
                    // Actualizar registro de devolución
                    Devolucione::where('dato_id', $vendido['dato']['id'])->update([
                        'unidades_resta' => $unidades_resta,
                        'total_resta' => $total_resta
                    ]);
                }
                $todo += $total_base;
            }
            
            $total_pagar = $remision->total_pagar - $todo;
            $total_donacion = $remision->total_donacion + $todo;
            // Actualizar la remisión
            $remision->update([
                'total_donacion' => $total_donacion,
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
