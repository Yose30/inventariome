<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Payment;
use App\Register;
use App\Libro;
use App\Note;

class NoteController extends Controller
{
    //Crear nota
    public function store(Request $request){
        try{
            \DB::beginTransaction();
            $num = Note::get()->count() + 1;
            if($num < 10){
                $folio = 'A000'.$num;
            }
            if($num >= 10 && $num < 100){
                $folio = 'A00'.$num;
            }
            if($num >= 100 && $num < 1000){
                $folio = 'A0'.$num;
            }
            if($num >= 1000 && $num < 10000){
                $folio = 'A'.$num;
            }
            $note = Note::create([
                'folio'     => $folio,
                'cliente'   => $request->cliente
            ]);
            $total = 0;
            foreach($request->registers as $register){
                Register::create([
                    'note_id' => $note->id,
                    'libro_id' => $register['id'],
                    'costo_unitario' => $register['costo_unitario'],
                    'unidades' => $register['unidades'],
                    'total' => $register['total'],
                    'unidades_pendiente' => $register['unidades'],
                    'total_pendiente' => $register['total']
                ]);
                
                $libro = Libro::whereId($register['id'])->first();
                $libro->update(['piezas' => $libro->piezas - $register['unidades']]);
                $total += $register['total'];
            }

            $note->update(['total_salida' => $total, 'total_pagar' => $total]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json($note);
    }

    //Mostrar notas
    public function show(){
        $notes = Note::orderBy('folio','desc')->get();
        return response()->json($notes);
    }

    //Mostrar detalles de nota
    public function detalles_nota(){
        $note_id = Input::get('note_id');
        $note = Note::whereId($note_id)->first();
        $registers = Register::where('note_id', $note->id)->with('libro')->with('payments')->get();
        return response()->json($registers);
    }

    //Guardar pago
    public function guardar_pago(Request $request){
        try{
            \DB::beginTransaction();
            
            $pagos = 0;
            foreach($request->registers as $register){
                $unidades = $register['unidades_base'];
                $total = $register['total_base'];

                $registro = Register::whereId($register['id'])->first();
                if($unidades != 0){
                    Payment::create([
                        'register_id' => $registro->id,
                        'unidades' => $unidades,
                        'pago' => $total
                    ]);
                }
                    
                $unidades_pagado = $registro->unidades_pagado + $unidades;
                $total_pagado = $registro->total_pagado + $total;
                $unidades_pendiente = $registro->unidades_pendiente - $unidades;
                $total_pendiente = $registro->total_pendiente - $total;

                $registro->update([
                    'unidades_pagado' => $unidades_pagado,
                    'total_pagado' => $total_pagado,
                    'unidades_pendiente' => $unidades_pendiente,
                    'total_pendiente' => $total_pendiente
                ]);
                $pagos += $total;
            }

            $nota = Note::whereId($request->id)->first();
            $total_pagar = $nota->total_pagar - $pagos;
            $total_pagos = $nota->pagos + $pagos;
            $nota->update([
                'total_pagar' => $total_pagar,
                'pagos' => $total_pagos
            ]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json($nota);
    }
}
