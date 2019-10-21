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
            $remision = Remisione::whereId($request->id)->first();
            $todo = 0;
            // $prueba = array();
            foreach($request->vendidos as $vendido){
                $unidades = $vendido['unidades_base'];
                $libro_id = $vendido['libro_id'];
                $costo_unitario = $vendido['dato']['costo_unitario'];
                $total = $unidades * $costo_unitario;
    
                if($unidades != 0){
                    Donacione::create([
                        'remisione_id' => $remision->id,
                        'libro_id' => $libro_id,
                        'unidades' => $unidades
                    ]);
                }

                $v = Vendido::whereId($vendido['id'])->first();
                
                $unidades_resta = $v->unidades_resta - $unidades;
                $total_resta = $unidades_resta * $costo_unitario;
                
                $v->update([
                    'unidades_resta' => $unidades_resta,
                    'total_resta' => $total_resta
                ]);
                Devolucione::where('dato_id', $vendido['dato']['id'])->update(['unidades_resta' => $unidades_resta]);
                $todo += $total;
            }
            $total_pagar = $remision->total_pagar - $todo;
            $total_donacion = $remision->total_donacion + $todo;
            $remision->update([
                'total_donacion' => $total_donacion,
                'total_pagar'   => $total_pagar
            ]);
            if ((int) $total_pagar === 0) {
                $remision->update(['estado' => 'Terminado']);
            }
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
        }
        
        return response()->json($remision);
    }
}
