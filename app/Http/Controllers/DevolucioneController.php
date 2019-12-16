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
            // Buscar remisión
            $remision = Remisione::whereId($request->id)->first();
            $entregado_por = $request->entregado_por;
            $total_devolucion = 0;
            
            foreach($request->devoluciones as $devolucion){
                $unidades_base = $devolucion['unidades_base'];
                $total_base = $devolucion['total_base'];

                if($unidades_base != 0){
                    // Buscar devolución
                    $d = Devolucione::whereId($devolucion['id'])->first();
                    // Buscar libro
                    $libro = Libro::whereId($d->libro->id)->first();

                    // Crear registro de fecha de la devolución
                    $fecha = Fecha::create([
                        'remisione_id' => $remision->id,
                        'fecha_devolucion' => Carbon::now()->format('Y-m-d'),
                        'libro_id' => $libro->id,
                        'unidades' => $unidades_base,
                        'total' => $total_base,
                        'entregado_por' => $entregado_por
                    ]);
    
                    $unidades = $d->unidades + $unidades_base;
                    $total = $d->total + $total_base;
                    $unidades_resta = $d->unidades_resta - $unidades_base;
                    $total_resta = $d->total_resta - $total_base;
                    // Actualizar la tabla de devolución
                    $d->update([
                        'unidades' => $unidades, 
                        'unidades_resta' => $unidades_resta,
                        'total' => $total,
                        'total_resta' => $total_resta
                    ]);
                    // Actualizar las piezas del libro
                    $libro->update(['piezas' => $libro->piezas + $unidades_base]);     
                    // Actualizar tabla vendidos
                    Vendido::where('dato_id', $d->dato->id)->update([
                        'unidades_resta' => $unidades_resta,
                        'total_resta'    => $total_resta
                    ]); 
                }  
                $total_devolucion += $total_base;
            }
            
            $total_pagar = $remision->total_pagar - $total_devolucion;
            $t_devolucion = $remision->total_devolucion + $total_devolucion;
            
            $remision->update([
                'total_devolucion' => $t_devolucion,
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
