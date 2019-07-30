<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Register;
use App\Libro;
use App\Note;

class NoteController extends Controller
{
    //Crear nota
    public function store(Request $request){
        try{
            \DB::beginTransaction();
            $note = Note::create(['cliente' => $request->cliente]);
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
        $notes = Note::all();
        return response()->json($notes);
    }

    //Mostrar detalles de nota
    public function detalles_nota(){
        $note_id = Input::get('note_id');
        $note = Note::whereId($note_id)->first();
        $registers = Register::where('note_id', $note->id)->with('libro')->get();
        return response()->json($registers);
    }
}
