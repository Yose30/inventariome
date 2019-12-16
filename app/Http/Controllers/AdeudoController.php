<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Devolucione;
use App\Remisione;
use App\Vendido;
use App\Adeudo;
use App\Libro;
use App\Abono;
use App\Dato;

class AdeudoController extends Controller
{
    // BUSCAR ADEUDO POR NUMERO DE REMISIÓN
    // Función utilizada en - AdeudosComponent - DevolucionAdeudosComponent
    public function buscar_adeudo(){
        $num_remision = Input::get('num_remision');
        $adeudo = Adeudo::where('remision_num', $num_remision)->with('cliente')->with('abonos')->first();
        // if(auth()->user()->role_id == 3){
        //     $adeudo = Adeudo::where('remision_num', $num_remision)
        //                     ->where('total_pendiente', '>', 0)
        //                     ->with('cliente')->with('abonos')->first();
        // }
        // else{
            
        // }
        return response()->json($adeudo);
    }

    // BUSCAR ADEUDO POR CLIENTE
    // Función utilizada en - AdeudosComponent - DevolucionAdeudosComponent
    public function adeudos_cliente(){
        $cliente_id = Input::get('cliente_id');
        $adeudos = Adeudo::where('cliente_id', $cliente_id)->with('cliente')->with('abonos')->get();
        // if(auth()->user()->role_id == 3){
        //     $adeudos = Adeudo::where('cliente_id', $cliente_id)
        //                     ->where('total_pendiente', '>', 0)
        //                     ->with('cliente')->with('abonos')->get();
        // }
        // else{
            
        // }
        
        return response()->json($adeudos);
    }

    // MOSTRAR LOS DETALLES DE UN ADEUDO
    // Función utilizada en - AdeudosComponent - DevolucionAdeudosComponent
    public function detalles_adeudo(){
        $id = Input::get('id');
        $adeudo = Adeudo::whereId($id)->with('cliente')->with('abonos')->first();
        $datos = Dato::where('remisione_id', $adeudo->remision_num)->with('libro')->get();
        $devoluciones = Devolucione::where('remisione_id', $adeudo->remision_num)->with('dato')->with('libro')->get();
        return response()->json([
            'adeudo' => $adeudo, 
            'datos' => $datos, 
            'devoluciones' => $devoluciones
        ]);
    }

    // REGISTRAR UN NUEVO ABONO A ADEUDO
    // Función utilizada en - AdeudosComponent - DevolucionAdeudosComponent
    public function guardar_abono(Request $request){
        try {
            \DB::beginTransaction();
                $adeudo = Adeudo::whereId($request->adeudo_id)->first();
                $total_abonos = $adeudo->total_abonos + $request->pago;
                $total_pendiente = $adeudo->total_pendiente - $request->pago;
                $adeudo->update([
                    'total_abonos' => $total_abonos,
                    'total_pendiente' => $total_pendiente
                ]);
                $abono = Abono::create($request->input());

                if($total_pendiente == 0){
                    Devolucione::where('remisione_id', $adeudo->remision_num)->update([
                        'unidades_resta' => 0,
                        'total_resta' => 0
                    ]);
                }

            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($adeudo);
    }

    // GUARDAR UN ADEUDO
    // Función utilizada en - AdeudosComponent - DevolucionAdeudosComponent
    public function store(Request $request){
        try {
            \DB::beginTransaction();
                $adeudo = Adeudo::create($request->input());
                foreach($request->datos as $dato){
                    $r_dato = Dato::create([
                        'remisione_id' => $request->remision_num,
                        'libro_id'  => $dato['id'],
                        'costo_unitario' => $dato['costo_unitario'],
                        'unidades'  => $dato['unidades'],
                        'total'     => $dato['total']
                    ]);
                    $devolucion = Devolucione::create([
                        'remisione_id' => $request->remision_num,
                        'dato_id'   => $r_dato->id,
                        'libro_id' => $dato['id'],
                        'unidades_resta' => $dato['unidades'],
                        'total_resta' => $dato['total'],
                    ]);
                }
                $t_adeudo = Adeudo::whereId($adeudo->id)->with('cliente')->first();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($t_adeudo);
    }

    // BUSCAR NUMERO DE REMISIÓN
    // Función utilizada en - AdeudosComponent - DevolucionAdeudosComponent
    public function buscarRemision(){
        $remision_num = Input::get('remision_num');
        $adeudo = Adeudo::where('remision_num', $remision_num)->count();
        $remision = Remisione::where('id', $remision_num)->count();
        return response()->json(['adeudo' => $adeudo, 'remision' => $remision]);
    }

    // GUARDAR DEVOLUCIÓN DE ADEUDO
    // Función utilizada en DevolucionAdeudoComponent
    public function guardar_adeudo_devolucion(Request $request){
        try {
            \DB::beginTransaction();
                $total_devolucion = 0;
                foreach($request->devoluciones as $devolucion){
                    $costo_unitario = $devolucion['dato']['costo_unitario'];
                    $unidades = $devolucion['unidades'];
                    $total = $unidades * $costo_unitario;
                    
                    if($unidades !== 0){
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
                    }
                    $total_devolucion += $total; 
                }
                $adeudo = Adeudo::whereId($request->id)->first();
                $total_pendiente = $adeudo->total_pendiente - $total_devolucion;
                $adeudo->update([
                    'total_pendiente' => $total_pendiente,
                    'total_devolucion' => $total_devolucion
                ]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($adeudo);
    }
}
