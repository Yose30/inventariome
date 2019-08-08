<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Registro;
use App\Entrada;
use App\Libro;
use PDF;
use Carbon\Carbon;

class EntradaController extends Controller
{
    //Mostrar todas las entradas
    public function show(){
        $entradas = Entrada::with('registros')->get();
        return response()->json($entradas);
    }

    //Mostrar entradas por fecha
    public function fecha_entradas(){
        $fecha1 	= new Carbon(Input::get('fecha1'));
        $fecha1 	= $fecha1->format('Y-m-d 00:00:00');
        $fecha2 	= new Carbon(Input::get('fecha1'));
        $fecha2 	= $fecha2->format('Y-m-d 23:59:59');  

        $entradas = Entrada::whereBetween('created_at', [$fecha1, $fecha2])->get();
        return response()->json($entradas);
    }

    //Buscar folio
    public function buscarFolio(){
        $folio = Input::get('folio');
        $entrada = Entrada::where('folio', $folio)->first();
        return response()->json($entrada);
    }

    //Mostrar editoriales
    public function mostrarEditoriales(){
        $editorial = Input::get('editorial');
        $entradas = Entrada::where('editorial','like','%'.$editorial.'%')->get();
        return response()->json($entradas);
    }

    //Mostrar detalles de una entrada
    public function detalles_entrada(){
        $entrada_id = Input::get('entrada_id');
        try {
            \DB::beginTransaction();
                $this->func_inicializar_editar($entrada_id);
                Registro::where('entrada_id', $entrada_id)->where('estado', 'Iniciado')->delete();
            \DB::commit();
        
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($e->getMessage());
        }
        $entrada = Entrada::whereId($entrada_id)->first();
        $registros = Registro::where('entrada_id', $entrada->id)->where('estado', '!=', 'Eliminado')->with('libro')->get();
        
        return response()->json(['entrada' => $entrada, 'registros' => $registros]);
    }

    public function nueva(){
        // $entrada = Entrada::all()->count() + 1;

        // try {
        //     \DB::beginTransaction();

        //     //En caso de edicion
        //     $this->func_inicializar_editar($entrada - 1);

        //     //En caso de creacion
        //     $eliminados = Registro::where('entrada_id', $entrada)->where('estado', 'Eliminado')->get();

        //     if($eliminados->count() > 0){
        //         foreach($eliminados as $eliminado){
        //             $libro = Libro::whereId($eliminado->libro_id)->first();
        //             $libro->update(['piezas' => $libro->piezas - $eliminado->unidades]);
        //         }
        //     }
        
        //     $registros = Registro::where('entrada_id', $entrada)->where('estado', 'Iniciado')->get();
        //     if($registros->count() > 0){
        //         foreach($registros as $registro){
        //             $libro = Libro::whereId($registro->libro_id)->first();
        //             $libro->update(['piezas' => $libro->piezas - $registro->unidades]);
        //         }
        //     }
            
        //     Registro::where('entrada_id', $entrada - 1)->where('estado', 'Iniciado')->delete();
        //     Registro::where('entrada_id', $entrada)->where('estado', 'Eliminado')->delete();
        //     Registro::where('entrada_id', $entrada)->where('estado', 'Iniciado')->delete();
        
        //     \DB::commit();
            
        // }catch (Exception $e) {
        //     \DB::rollBack();
        //     return response()->json($e->getMessage());
        // }
        // return response()->json(null, 200);
    }

    public function func_inicializar_editar($entrada){
        Registro::where('entrada_id', $entrada)
        ->where('estado', 'Eliminado')
        ->update(['estado' => 'Terminado']);

        //En caso de haber agregado uno nuevo, se recuperan las piezas
        $noguardados = Registro::where('entrada_id', $entrada)->where('estado', 'Iniciado')->get();
        if($noguardados->count() > 0){
            foreach($noguardados as $noguardado){
                $libro = Libro::whereId($noguardado->libro_id)->first();
                $libro->update(['piezas' => $libro->piezas - $noguardado->unidades]);
            }
        }
    }

    public function registro(Request $request){
        // if($request->entrada_id != 0){
        //     $entrada = $request->entrada_id;
        // }
        // else{
        //     $entrada = Entrada::all()->count() + 1;
        // }

        // try {
        //     \DB::beginTransaction();

        //     $registro = Registro::create([
        //         'entrada_id' => $entrada,
        //         'libro_id'  => $request->id,
        //         'costo_unitario' => $request->costo_unitario,
        //         'unidades'  => $request->unidades,
        //         'total'     => $request->total
        //     ]);

        //     $libro = Libro::whereId($registro->libro_id)->first();
        //     $libro->update(['piezas' => $libro->piezas + $registro->unidades]);

        //     \DB::commit();

        //     return response()->json(['registro' => $registro, 'libro' => $registro->libro]);

        // } catch (Exception $e) {
        //     \DB::rollBack();
        //     return response()->json($e->getMessage());
		// }
    }

    public function eliminar(Request $request){
        try {
            \DB::beginTransaction();

            $registro = Registro::whereId($request->id)->update(['estado' => 'Eliminado']);

            \DB::commit();
            
            return response()->json($registro);
        
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function store(Request $request){
        try {
            \DB::beginTransaction();

            $entrada = Entrada::create([
                'folio' => $request->folio,
                'editorial' => $request->editorial,
                'unidades' => $request->unidades,
            ]);
            $unidades = 0;
            foreach($request->items as $item){
                $registro = Registro::create([
                    'entrada_id' => $entrada->id,
                    'libro_id'  => $item['id'],
                    'unidades'  => $item['unidades'],
                    'estado'    => 'Terminado'
                ]);
    
                $libro = Libro::whereId($item['id'])->first();
                $libro->update(['piezas' => $libro->piezas + $item['unidades']]);
                $unidades += $item['unidades'];
            }
            $entrada->update(['unidades' => $unidades]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($entrada);
    }

    public function actualizar(Request $request){
        $entrada = Entrada::whereId($request->id)->first();
        try {
            \DB::beginTransaction();

            $this->concluir_registro($entrada->id);
            
            foreach($request->nuevos as $nuevo){
                Registro::create([
                    'entrada_id' => $entrada->id,
                    'libro_id'  => $nuevo['id'],
                    'unidades'  => $nuevo['unidades'],
                    'estado'    => 'Terminado'
                ]);
    
                $libro = Libro::whereId($nuevo['id'])->first();
                $libro->update(['piezas' => $libro->piezas + $nuevo['unidades']]);
            }

            $entrada->folio = $request->folio;
            $entrada->editorial = $request->editorial;
            $entrada->unidades = $request->unidades;
            $entrada->save();

            \DB::commit();

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($entrada);
    }

    public function concluir_registro($id){
        $registros = Registro::where('entrada_id', $id)->where('estado', 'Eliminado')->get();
        foreach($registros as $registro){
            $libro = Libro::whereId($registro->libro_id)->first();
            $libro->update(['piezas' => $libro->piezas - $registro->unidades]);
        }

        Registro::where('entrada_id', $id)->where('estado', 'Eliminado')->delete();
    }

    public function actualizar_costos(Request $request){
        $total = 0;
        try {
            \DB::beginTransaction();
            foreach($request->items as $item){
                Registro::whereId($item['id'])->update([
                    'costo_unitario' => $item['costo_unitario'],
                    'total' => $item['total']
                ]);
                $total += $item['total'];
            }
            $entrada = Entrada::whereId($request->id)->first();
            $entrada->total = $total;
            $entrada->save();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($entrada);
    }

    public function imprimirEntrada($id){
        $entrada = Entrada::whereId($id)->first();
        $data['entrada'] = $entrada;

        $registros = Registro::where('entrada_id', $entrada->id)->with('libro')->get();
        $data['entrada'] = $entrada;
        $data['registros'] = $registros;
        $pdf = PDF::loadView('inventario.entrada', $data); 
        
        return $pdf->download('entrada.pdf');
    }

    public function pago_entrada(Request $request){
        $prueba = array();
        foreach($request->items as $item){
            // $item['unidades_base']
            // $item['total_base']
            array_push($prueba, $item['unidades_base']);
        }
        return response()->json($request->id);
    }
}
