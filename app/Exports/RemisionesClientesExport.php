<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Remisione;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RemisionesClientesExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($cliente_id, $inicio, $final)
    {
        $this->cliente_id = $cliente_id;
        $this->inicio = $inicio;
        $this->final = $final;
    }

    public function view(): View
    {
        $valores = $this->totales($this->get_remisiones());
        return view('remision.excel.reporte-cliente-gral', [
            'fecha' => $valores['fecha'],
            'inicio' => $this->inicio,
            'final' => $this->final,
            'remisiones' => $this->get_remisiones(),
            'totales' => $valores['totales']
        ]);
    }

    public function get_remisiones(){
        if($this->inicio != '0000-00-00' && $this->final != '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $this->cliente_id)
                            ->whereBetween('fecha_creacion', [$this->inicio, $this->final])
                            ->whereNotIn('estado', ['Iniciado', 'Cancelado'])
                            ->orderBy('id','desc')
                            ->get();
        }
        else {
            $remisiones = Remisione::where('cliente_id', $this->cliente_id)
                    ->whereNotIn('estado', ['Iniciado', 'Cancelado'])
                    ->orderBy('id','desc')
                    ->get();
        }
        
        return $remisiones;
    }

    public function totales($remisiones){
        $total_salida = 0;
        $total_pagos = 0;
        $total_devolucion = 0;
        $total_donacion = 0;
        $total_pagar = 0;

        foreach($remisiones as $r){
            $total_salida += $r->total;
            $total_pagos += $r->pagos;
            $total_devolucion += $r->total_devolucion;
            $total_donacion += $r->total_donacion;
            $total_pagar += $r->total_pagar;            
        }
        $datos = [
            'fecha' => Carbon::now(),
            'totales' => [
                'total_salida' => $total_salida,
                'total_pagos' => $total_pagos,
                'total_devolucion' => $total_devolucion,
                'total_donacion' => $total_donacion,
                'total_pagar' => $total_pagar
            ]
        ];
        return $datos;
    }
}
