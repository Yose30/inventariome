<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Devolucione;
use Carbon\Carbon;
use App\Remisione;
use App\Vendido;
use App\Cliente;
use App\Libro;
use App\Fecha;
use App\Dato;

class DevolucioneController extends Controller
{
    // GUARDAR DEVOLUCIÓN DE REMISIÓN
    // Función utilizada en DevolucionController
    public function concluir(Request $request){
        try {
            \DB::beginTransaction();
            $remision = Remisione::whereId($request->id)->first();
            $total_devolucion = 0;
            foreach($request->devoluciones as $devolucion){
                $costo_unitario = $devolucion['dato']['costo_unitario'];

                $unidades_base = $devolucion['unidades_base'];
                $total_base = $unidades_base * $costo_unitario;
                
                $unidades_resta = $devolucion['unidades_resta'] - $unidades_base;
                $total_resta = $unidades_resta * $costo_unitario;

                $libro = Libro::whereId($devolucion['libro']['id'])->first();
               
                $fecha = Fecha::create([
                    'remisione_id' => $remision->id,
                    'fecha_devolucion' => Carbon::now()->format('Y-m-d'),
                    'libro_id' => $libro->id,
                    'unidades' => $unidades_base,
                    'total' => $total_base
                ]);

                $d = Devolucione::whereId($devolucion['id'])->first();
                $unidades = $d->unidades + $unidades_base;
                $total = $d->total + $total_base;
                
                $d->update([
                    'unidades' => $unidades, 
                    'unidades_resta' => $unidades_resta,
                    'total' => $total,
                    'total_resta' => $total_resta
                ]);
                
                $libro->update(['piezas' => $libro->piezas + $unidades_base]);     
                
                Vendido::where('dato_id', $devolucion['dato']['id'])->update([
                    'unidades_resta' => $unidades_resta,
                    'total_resta'    => $total_resta
                ]);   
                $total_devolucion += $total_base;
            }
            
            $total_pagar = $remision->total_pagar - $total_devolucion;
            
            $remision->update([
                'total_devolucion' => $remision->total_devolucion + $total_devolucion,
                'total_pagar'   => $total_pagar
            ]);
            if ((int) $total_pagar === 0) {
                $remision->update(['estado' => 'Terminado']);
            }
            \DB::commit();

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($remision);
    } 
    
    //Mostrar todas las devoluciones (ELIMINADA)
    public function all_devoluciones(){
        $remisiones = Remisione::where('total_pagar', '>', '0')
                                ->where(function ($query) {
                                    $query->where('estado', 'Proceso')
                                            ->orWhere('estado', 'Terminado');
                                })->orderBy('id','desc')
                                ->with('cliente')->get(); 
        return response()->json($remisiones);
    }
}
