<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Remisione;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RemisionesFechasExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($inicio, $final)
    {
        $this->inicio = $inicio;
        $this->final = $final;
    }

    public function view(): View
    {
        $remisiones = $this->remisiones_PF($this->inicio, $this->final);
        $valores = $this->totales($remisiones);
        return view('remision.excel.reporte-fecha-gral', [
            'remisiones' => $remisiones,
            'inicio' => $this->inicio,
            'final' => $this->final,
            'fecha' => $valores['fecha'],
            'totales' => $valores['totales']
        ]);
    }

    // OBTENER TOTALES DE LAS REMISIONES
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
            'fecha' => $fecha = Carbon::now(),
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

    // RETORNAR LA BUSQUEDA DE REMISIONES POR FECHA
    public function remisiones_PF($inicio, $final){
        return Remisione::whereBetween('fecha_creacion', [$inicio, $final])
                            ->orderBy('cliente_id','asc')
                            ->with('cliente')->get();
    }
}
