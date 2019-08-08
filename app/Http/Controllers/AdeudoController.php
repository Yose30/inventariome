<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Adeudo;
use App\Abono;

class AdeudoController extends Controller
{
    public function store(Request $request){
        try {
            \DB::beginTransaction();
                $adeudo = Adeudo::create($request->input());
                $dato = Adeudo::whereId($adeudo->id)->with('cliente')->first();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($dato);
    }

    public function show(){
        $adeudos = Adeudo::with('cliente')->with('abonos')->get();
        return response()->json($adeudos);
    }

    public function detalles_adeudo(){
        $id = Input::get('id');
        $adeudo = Adeudo::whereId($id)->with('cliente')->with('abonos')->first();
        return response()->json($adeudo);
    }

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

            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($adeudo);
    }

    public function adeudos_cliente(){
        $cliente_id = Input::get('cliente_id');
        $adeudos = Adeudo::where('cliente_id', $cliente_id)->with('cliente')->with('abonos')->get();
        return response()->json($adeudos);
    }
}
