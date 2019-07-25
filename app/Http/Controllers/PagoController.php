<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Remisione;
use App\Pago;

class PagoController extends Controller
{
    public function store(Request $request){
        $remision = Remisione::whereId($request->id)->first();
        if(auth()->user()->role_id == 2){
            $tipo = 'transferencia';
        }
        if(auth()->user()->role_id == 3){
            $tipo = 'efectivo';
        }
        $pago = Pago::create([
            'user_id' => auth()->user()->id,
            'remision_id' => $remision->id,
            'pago' => $request->pago,
            'tipo' => $tipo
        ]);

        $remision->pagos = $remision->pagos + $pago->pago;
        $remision->save();

        return response()->json($remision);
    }
}
