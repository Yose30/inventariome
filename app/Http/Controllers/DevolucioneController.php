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
use App\Dato;

class DevolucioneController extends Controller
{
    //Mostrar todas las devoluciones
    public function all_devoluciones(){
        $remisiones = Remisione::where('total_pagar', '>', '0')
                                ->where(function ($query) {
                                    $query->where('estado', 'Proceso')
                                            ->orWhere('estado', 'Terminado');
                                })->orderBy('id','desc')
                                ->with('cliente')->get(); 
        return response()->json($remisiones);
    }

    //Obtener los datos de una devolución
    public function datos_devolucion(){
        $remision_id = Input::get('remision_id');
        $datos = Dato::where('remisione_id', $remision_id)->with('libro')->get();
        $devoluciones = Devolucione::where('remisione_id', $remision_id)->with('libro')->with('dato')->get();
        return response()->json(['datos' => $datos, 'devoluciones' => $devoluciones]);
    }

    //Registrar entrega de libros, llenando tabla con de devoluciones con datos
    public function registrar_datos(){
        $remision_id = Input::get('remision_id');
        $remision = Remisione::whereId($remision_id)->first();
        $devoluciones = Devolucione::where('remisione_id', $remision->id)->with('libro')->with('dato')->get();
        return response()->json(['devoluciones' => $devoluciones, 'remision' => $remision]);
    }

    public function actualizar(Request $request){
        $devolucion = Devolucione::whereId($request->id)->with('libro')->with('dato')->first();
        $remision = Remisione::whereId($devolucion->remisione_id)->first();

        try {
            \DB::beginTransaction();
            
            
            // $total_devolucion = 0;

            // foreach($devoluciones as $d){
            //     $total_devolucion += $d->total;            
            // }
            // $con_descuento = ($total_devolucion) - (($total_devolucion * $descuento) / 100);
            // $remision->total_devolucion = $con_descuento;
            // $remision->total_pagar = $remision->total - $con_descuento;
            // $remision->save();

            \DB::commit();
            
            return response()->json([
                'devolucion' => $devolucion, 
                'total_devolucion' => $remision->total_devolucion, 
                'total_pagar' => $remision->total_pagar]);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
        
    }

    public function concluir(Request $request){
        try {
            \DB::beginTransaction();
            $total_devolucion = 0;
            foreach($request->devoluciones as $devolucion){
                $costo_unitario = $devolucion['dato']['costo_unitario'];
                $unidades = $devolucion['unidades'];
                $total = $unidades * $costo_unitario;
                
                $unidades_resta = $devolucion['unidades_resta'] - $unidades;
                $total_resta = $unidades_resta * $costo_unitario;
                
                Devolucione::whereId($devolucion['id'])->update([
                    'unidades' => $unidades, 
                    'unidades_resta' => $unidades_resta,
                    'total' => $total,
                    'total_resta' => $total_resta
                ]);
                
                $libro = Libro::whereId($devolucion['libro']['id'])->first();
                $libro->update(['piezas' => $libro->piezas + $unidades]);     
                
                Vendido::where('dato_id', $devolucion['dato']['id'])->update([
                    'unidades_resta' => $unidades_resta,
                    'total_resta'    => $costo_unitario * $unidades_resta
                ]);   
                $total_devolucion += $total;
            }
            
            $remision = Remisione::whereId($request->id)->first();
            $total_pagar = $remision->total - ($remision->pagos + $total_devolucion);
            $remision->update([
                'estado' => 'Terminado', 
                'fecha_devolucion' => Carbon::now()->format('Y-m-d'),
                'total_devolucion' => $total_devolucion,
                'total_pagar'   => $total_pagar
            ]);
            \DB::commit();

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($remision);
    }

    public function todos(){
        $devoluciones = Devolucione::with('remision')->with('libro')->get();
        return response()->json($devoluciones);
    }
}
