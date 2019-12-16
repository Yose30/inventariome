<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Cliente;
use App\Remisione;
use App\Dato;

class ClienteController extends Controller
{
    // OBTENER TODOS LOS CLIENTES
    // Función utilizada en AdeudosComponent, RemisionComponent
    public function getTodo(){
        $clientes = Cliente::orderBy('name', 'asc')->get();
        return response()->json($clientes);
    }
    
    // MOSTRAR TODOS LOS CLIENTES
    // Función utilizada en los componentes
    // - AdeudosComponent - ClientesComponent - DevolucionAdeudosComponent
    // - DevolucionComponent - ListadoComponent - PagosComponent - RemisionComponent - RemisionesComponent
    public function show(){
        $queryCliente = Input::get('queryCliente');
        $clientes = Cliente::where('name','like','%'.$queryCliente.'%')->orderBy('name', 'asc')->get();
        return response()->json($clientes);
    }

    // EDITAR DATOS DE CLIENTE
    // Función utilizada en ClientesComponent
    public function editar(Request $request){
        $cliente = Cliente::whereId($request->id)->first();
        $cliente->name = 'PRUEBA';
        $cliente->save();
        $this->validacion($request);
        try {
            \DB::beginTransaction();
            $cliente->update([
                'name' => strtoupper($request->name),
                'contacto' => strtoupper($request->contacto),
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => strtoupper($request->direccion),
                'condiciones_pago' => strtoupper($request->condiciones_pago)
            ]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($cliente);
    }

    // GUARDAR NUEVO CLIENTE
    // Función utilizada en NewClienteComponent
    public function store(Request $request){
        $this->validacion($request);
        try {
            \DB::beginTransaction();
            // $request->input()
            $cliente = Cliente::create([
                'name' => strtoupper($request->name),
                'contacto' => strtoupper($request->contacto),
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => strtoupper($request->direccion),
                'condiciones_pago' => strtoupper($request->condiciones_pago)
            ]);
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

    public function detallesCliente(){
        $cliente_id = Input::get('cliente_id');
        $cliente = Cliente::whereId($cliente_id)->first();
        return response()->json($cliente);
    }
}
