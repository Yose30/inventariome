<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Cliente;
use App\Remisione;
use App\Dato;

class ClienteController extends Controller
{
    //Nuevo cliente
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'min:3|max:100|required|string',
            'email' => 'min:8|max:50|required|email',
            'telefono' => 'required|numeric|max:9999999999|min:1000000',
            'direccion' => 'min:3|max:150|required|string',
            'descuento' => 'numeric|required|min:1|max:99',
            'condiciones_pago' => 'min:3|max:150|required|string'
        ]);
        
        try {
            \DB::beginTransaction();
            
            $cliente = Cliente::create($request->input());

            \DB::commit();

            return response()->json(null, 200);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }
    
    public function show(){
        $queryCliente = Input::get('queryCliente');
        $clientes = Cliente::where('name','like','%'.$queryCliente.'%')->get();
        
        $remision = Remisione::all()->count() + 1;

        try {
            \DB::beginTransaction();

            Dato::where('remision_id', $remision)->delete();

            \DB::commit();

        } catch (Exception $e) {
            \DB::rollBack();
		}

        return response()->json($clientes);
    }
}
