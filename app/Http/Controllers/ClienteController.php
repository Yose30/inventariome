<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Cliente;
use App\Remisione;
use App\Dato;

class ClienteController extends Controller
{
    // MOSTRAR TODOS LOS CLIENTES
    // Función utilizada en los componentes
    // - AdeudosComponent - ClientesComponent - DevolucionAdeudosComponent
    // - DevolucionComponent - ListadoComponent - PagosComponent - RemisionComponent - RemisionesComponent
    public function show(){
        $queryCliente = Input::get('queryCliente');
        $clientes = Cliente::where('name','like','%'.$queryCliente.'%')->get();
        return response()->json($clientes);
    }

    // EDITAR DATOS DE CLIENTE
    // Función utilizada en ClientesComponent
    public function editar(Request $request){
        $cliente = Cliente::whereId($request->id)->first();
        $cliente->name = 'prueba';
        $cliente->save();
        $this->validacion($request);
        try {
            \DB::beginTransaction();
            $cliente->name = $request->name;
            $cliente->contacto = $request->contacto;
            $cliente->email = $request->email;
            $cliente->telefono = $request->telefono;
            $cliente->direccion = $request->direccion;
            $cliente->condiciones_pago = $request->condiciones_pago;
            $cliente->save();
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($cliente);
    }

    // OBTENER TODOS LOS CLIENTES
    // Función utilizada en AdeudosComponent, RemisionComponent
    public function getTodo(){
        $clientes = Cliente::get();
        return response()->json($clientes);
    }

    // GUARDAR NUEVO CLIENTE
    // Función utilizada en NewClienteComponent
    public function store(Request $request){
        $this->validacion($request);
        try {
            \DB::beginTransaction();
            $cliente = Cliente::create($request->input());
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($cliente);
    }

    public function validacion($request){
        $this->validate($request, [
            'name' => 'min:3|max:100|required|string|unique:clientes',
            'email' => 'min:8|max:50|required|email',
            'telefono' => 'required|numeric|max:9999999999|min:1000000',
            'direccion' => 'min:3|max:150|required|string',
            'condiciones_pago' => 'min:3|max:150|required|string'
        ]);
    }
}
