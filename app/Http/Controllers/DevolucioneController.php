<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Dato;
use App\Devolucione;
use App\Remisione;
use App\Cliente;

class DevolucioneController extends Controller
{
    public function show(){
        $remision_id = Input::get('remision_id');
        $datos = Dato::where('remision_id', $remision_id)->get();
        
        try {

            if(Devolucione::where('remision_id', $remision_id)->count() == 0){
                
                \DB::beginTransaction();

                Remisione::whereId($remision_id)->update(['estado' => 'Proceso']);

                foreach($datos as $dato){
                    $devolucion = Devolucione::create([
                        'remision_id' => $dato->remision_id,
                        'dato_id'   => $dato->id,
                        'clave_libro' => $dato->isbn_libro,
                        'titulo'    => $dato->titulo,
                        'costo_unitario' => $dato->costo_unitario
                    ]);
                }
                \DB::commit();
            }
        
            $devoluciones = Devolucione::where('remision_id', $remision_id)->get();
            return response()->json($devoluciones);
        
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function actualizar(Request $request){
        $devolucion = Devolucione::whereId($request->id)->first();
        $dato = Dato::whereId($devolucion->dato_id)->first();
        $remision = Remisione::whereId($devolucion->remision_id)->first();
        $cliente = Cliente::whereId($remision->cliente_id)->first();

        $unidades = $request->unidades;
        $costo_unitario = $devolucion->costo_unitario;
        $descuento = $cliente->descuento;

        $total = ($unidades * $costo_unitario) - ((($unidades * $costo_unitario) * $descuento) / 100);
        $total_resta = $dato->total - $total;

        try {
            \DB::beginTransaction();
            
            $devolucion->unidades = $unidades;
            $devolucion->total = $total;
            $devolucion->total_resta = $total_resta;
            $devolucion->save();

            $devoluciones = Devolucione::where('remision_id', $devolucion->remision_id)->get();

            $total_devolucion = 0;

            foreach($devoluciones as $d){
                $total_devolucion += $d->total;            
            }

            $remision->total_devolucion = $total_devolucion;
            $remision->total_pagar = $remision->total - $total_devolucion;
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
            
            Remisione::whereId($request->id)->update(['estado' => 'Terminado']);

            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();

            return response()->json($exception->getMessage());
        }
        
        return response()->json(null, 200);
    }
}
