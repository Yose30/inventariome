<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Dato;
use App\Devolucione;
use App\Remisione;
use App\Cliente;
use App\Libro;

class DevolucioneController extends Controller
{
    public function show(){
        $remision_id = Input::get('remision_id');
        $remision = Remisione::whereId($remision_id)->first();
        $datos = Dato::where('remision_id', $remision->id)->with('libro')->get();
        
        try {

            if(Devolucione::where('remision_id', $remision_id)->count() == 0){
                
                \DB::beginTransaction();

                $remision->update(['estado' => 'Proceso']);

                $total_pagar = 0;
                foreach($datos as $d){
                    $total_pagar += $d->total;            
                }

                $con_descuento = ($total_pagar) - (($total_pagar * $remision->cliente->descuento) / 100);

                $remision->update(['total_pagar' => $con_descuento]);

                foreach($datos as $dato){
                    $devolucion = Devolucione::create([
                        'remision_id' => $dato->remision_id,
                        'dato_id'   => $dato->id,
                        'libro_id' => $dato->libro_id,
                        'unidades_resta' => $dato->unidades,
                        'total_resta' => $dato->total,
                    ]);
                }

                \DB::commit();
            }
        
            $devoluciones = Devolucione::where('remision_id', $remision_id)->with('libro')->with('dato')->get();
            return response()->json(['devoluciones' => $devoluciones, 'remision' => $remision]);
        
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function actualizar(Request $request){
        $devolucion = Devolucione::whereId($request->id)->with('libro')->with('dato')->first();
        $remision = Remisione::whereId($devolucion->remision_id)->first();

        $unidades = $request->unidades;
        $unidades_resta = $devolucion->dato->unidades - $unidades;
        $costo_unitario = $devolucion->dato->costo_unitario;
        $descuento = $remision->cliente->descuento;

        //$total = ($unidades * $costo_unitario) - ((($unidades * $costo_unitario) * $descuento) / 100);
        $total = $unidades * $costo_unitario;
        $total_resta = $devolucion->dato->total - $total;

        try {
            \DB::beginTransaction();
            
            $devolucion->unidades = $unidades;
            $devolucion->unidades_resta = $unidades_resta;
            $devolucion->total = $total;
            $devolucion->total_resta = $total_resta;
            $devolucion->save();

            $devoluciones = Devolucione::where('remision_id', $devolucion->remision_id)->get();

            $total_devolucion = 0;

            foreach($devoluciones as $d){
                $total_devolucion += $d->total;            
            }
            $con_descuento = ($total_devolucion) - (($total_devolucion * $descuento) / 100);
            $remision->total_devolucion = $con_descuento;
            $remision->total_pagar = $remision->total - $con_descuento;
            $remision->save();

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
            
            $remision = Remisione::whereId($request->id)->first();
            $remision->update(['estado' => 'Terminado']);

            $devoluciones = Devolucione::where('remision_id', $remision->id)->get();

            foreach($devoluciones as $devolucion){
                $libro = Libro::whereId($devolucion->libro_id)->first();
                $libro->update(['piezas' => $libro->piezas + $devolucion->unidades]);
            }

            \DB::commit();

            return response()->json($remision);

        } catch (Exception $e) {
            \DB::rollBack();

            return response()->json($exception->getMessage());
        }
    }

    public function todos(){
        $devoluciones = Devolucione::with('remision')->with('libro')->get();
        return response()->json($devoluciones);
    }
}
