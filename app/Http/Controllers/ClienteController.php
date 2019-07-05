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
        $this->validacion($request);
        
        try {
            \DB::beginTransaction();
            
            $cliente = Cliente::create($request->input());

            \DB::commit();

            return response()->json($cliente);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function editar(Request $request){
        $cliente = Cliente::whereId($request->id)->first();
        $cliente->name = 'prueba';
        $cliente->save();
        
        $this->validacion($request);
        try {
            \DB::beginTransaction();
            
            $cliente->name = $request->name;
            $cliente->email = $request->email;
            $cliente->telefono = $request->telefono;
            $cliente->direccion = $request->direccion;
            // $cliente->descuento = $request->descuento;
            $cliente->condiciones_pago = $request->condiciones_pago;
            $cliente->save();

            \DB::commit();

            return response()->json($cliente);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function validacion($request){
        $this->validate($request, [
            'name' => 'min:3|max:100|required|string|unique:clientes',
            'email' => 'min:8|max:50|required|email',
            'telefono' => 'required|numeric|max:9999999999|min:1000000',
            'direccion' => 'min:3|max:150|required|string',
            // 'descuento' => 'numeric|required|min:0|max:99',
            'condiciones_pago' => 'min:3|max:150|required|string'
        ]);
    }
    
    public function show(){
        $queryCliente = Input::get('queryCliente');
        $clientes = Cliente::where('name','like','%'.$queryCliente.'%')->get();
    
        return response()->json($clientes);
    }

    public function getCliente(){
        $id = Input::get('id');
        $remision_id = Input::get('remision_id');
        $cliente = Cliente::whereId($id)->first();
        
        $datos = Dato::where('remision_id', $remision_id)->with('libro')->get();
        return response()->json(['cliente' => $cliente, 'datos' => $datos]);
    }

    public function getTodo(){
        $clientes = Cliente::all();
        return response()->json($clientes);
    }
}
