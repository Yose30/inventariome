<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Dato;
use App\Remisione;
use App\Devolucione;
use App\Cliente;
use PDF;

class RemisionController extends Controller
{
    public function registro(Request $request){
        if($request->remision_id != 0){
            $remision = $request->remision_id;
        }
        else{
            $remision = Remisione::all()->count() + 1;
        }
        
        try {
            \DB::beginTransaction();

            $datos = Dato::create([
                'remision_id' => $remision,
                'isbn_libro' => $request->ISBN,
                'titulo'    => $request->titulo,
                'unidades'  => $request->unidades,
                'costo_unitario' => $request->costo_unitario,
                'total'     => $request->total
            ]);

            \DB::commit();

            return response()->json($datos);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function store(Request $request){
        try {
            \DB::beginTransaction();

            $remision = Remisione::create([
                'cliente_id' => $request->cliente_id,
                'total' => $request->total,
                'fecha_entrega' => $request->fecha_entrega,
                'estado' => 1,
                'fecha_creacion' => Carbon::now()->format('Y-m-d')
            ]);

            \DB::commit();
        
            return response()->json($remision);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
        
    }

    public function show(){
        $numero = Input::get('numero');
        $remision = Remisione::whereId($numero)->first();
        $datos = Dato::where('remision_id', $remision->id)->get();
        return response()->json(['remision' => $remision, 'datos' => $datos]);
    }

    public function actualizar(Request $request){
        $remision = Remisione::whereId($request->id)->first();
        
        try {
            \DB::beginTransaction();

            $remision->fecha_entrega = $request->fecha_entrega;
            $remision->total = $request->total;
            $remision->save();

            \DB::commit();

            return response()->json($remision);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function eliminar(Request $request){
        try {
            \DB::beginTransaction();

            $dato = Dato::whereId($request->id)->delete();

            \DB::commit();
            
            return response()->json($dato);
        
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function todos(){
        $remisiones = Remisione::with('cliente')->get();
        return response()->json($remisiones);
    }

    public function por_numero(){
        $num_remision = Input::get('num_remision');
        $remision = Remisione::whereId($num_remision)->first();
        $cliente = Cliente::whereId($remision->cliente_id)->first();
        return response()->json(['remision' => $remision, 'cliente_nombre' => $cliente->name]);
    }

    public function por_cliente(){
        $id = Input::get('id');
        $inicio = Input::get('inicio');
        $final = Input::get('final');

        if($inicio != '' && $final != ''){
            $remisiones = Remisione::where('cliente_id', $id)->whereBetween('fecha_creacion', [$inicio, $final])->with('cliente')->get();
        }
        else{
            $remisiones = Remisione::where('cliente_id', $id)->with('cliente')->get();
        }

        return response()->json($remisiones);
    }

    public function por_fecha(){
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        $cliente_id = Input::get('cliente_id');

        if($cliente_id != 0){
            $remisiones = Remisione::where('cliente_id', $cliente_id)->whereBetween('fecha_creacion', [$inicio, $final])->with('cliente')->get();
        }
        else{
            $remisiones = Remisione::whereBetween('fecha_creacion', [$inicio, $final])->with('cliente')->get();
        }

        return response()->json($remisiones);
    }

    public function imprimirSalida(){
        //$remision_id = Input::get('remision_id');
        $remision = Remisione::whereId(1)->first();
        $datos = Dato::where('remision_id', $remision->id)->get();

        $data['remision'] = $remision;
        $data['datos'] = $datos;
        
        $pdf = PDF::loadView('remision.nota', $data);
        //return response()->json($pdf->download('Nota-remision.pdf'));
        return $pdf->download('Nota_remision.pdf');
        //return response()->json($data);
    }
}
