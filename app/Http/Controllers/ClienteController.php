<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cliente;

class ClienteController extends Controller
{
    //Nuevo cliente
    public function store(Request $request){
        $this->validate($request, [
            'name' => 'min:3|max:40|required|string',
            'last_name' => 'min:3|max:50|required|string',
            'email' => 'min:8|max:50|required|email',
            'telefono' => 'required|numeric|max:9999999999|min:1000000',
            'direccion' => 'min:3|max:150|required|string',
            'descuento' => 'numeric|required|min:1|max:99',
            'condiciones_pago' => 'min:3|max:150|required|string'
        ]);
        
        $cliente = Cliente::create($request->input());

        return response()->json(null, 200);
    }
}
