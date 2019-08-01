<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Registro;
use App\Entrada;
use App\Libro;
use PDF;

class EntradaController extends Controller
{
    //Mostrar todas las entradas
    public function show(){
        $entradas = Entrada::with('registros')->get();
        return response()->json($entradas);
    }

    //Buscar folio
    public function buscarFolio(){
        $folio = Input::get('folio');
        $entrada = Entrada::where('folio', $folio)->first();
        return response()->json($entrada);
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
        $entrada = Entrada::all()->count() + 1;

        try {
            \DB::beginTransaction();

            //En caso de edicion
            $this->func_inicializar_editar($entrada - 1);

            //En caso de creacion
            $eliminados = Registro::where('entrada_id', $entrada)->where('estado', 'Eliminado')->get();

            if($eliminados->count() > 0){
                foreach($eliminados as $eliminado){
                    $libro = Libro::whereId($eliminado->libro_id)->first();
                    $libro->update(['piezas' => $libro->piezas - $eliminado->unidades]);
                }
            }
        
            $registros = Registro::where('entrada_id', $entrada)->where('estado', 'Iniciado')->get();
            if($registros->count() > 0){
                foreach($registros as $registro){
                    $libro = Libro::whereId($registro->libro_id)->first();
                    $libro->update(['piezas' => $libro->piezas - $registro->unidades]);
                }
            }
            
            Registro::where('entrada_id', $entrada - 1)->where('estado', 'Iniciado')->delete();
            Registro::where('entrada_id', $entrada)->where('estado', 'Eliminado')->delete();
            Registro::where('entrada_id', $entrada)->where('estado', 'Iniciado')->delete();
        
            \DB::commit();
            
        }catch (Exception $e) {
            \DB::rollBack();
            return response()->json($e->getMessage());
        }
        return response()->json(null, 200);
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
        if($request->entrada_id != 0){
            $entrada = $request->entrada_id;
        }
        else{
            $entrada = Entrada::all()->count() + 1;
        }

        try {
            \DB::beginTransaction();

            $registro = Registro::create([
                'entrada_id' => $entrada,
                'libro_id'  => $request->id,
                'costo_unitario' => $request->costo_unitario,
                'unidades'  => $request->unidades,
                'total'     => $request->total
            ]);

            $libro = Libro::whereId($registro->libro_id)->first();
            $libro->update(['piezas' => $libro->piezas + $registro->unidades]);

            \DB::commit();

            return response()->json(['registro' => $registro, 'libro' => $registro->libro]);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($e->getMessage());
		}
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
                'total' => $request->total,
            ]);

            $this->concluir_registro($entrada->id);

            \DB::commit();

            return response()->json($entrada);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function actualizar(Request $request){
        $entrada = Entrada::whereId($request->id)->first();
        
        try {
            \DB::beginTransaction();
            $entrada->folio = $request->folio;
            $entrada->editorial = $request->editorial;
            $entrada->unidades = $request->unidades;
            $entrada->total = $request->total;
            $entrada->save();

            $this->concluir_registro($entrada->id);

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
        Registro::where('entrada_id', $id)->update(['estado' => 'Terminado']);
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
}
