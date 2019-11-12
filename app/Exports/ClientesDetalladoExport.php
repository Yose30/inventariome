<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Remisione;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ClientesDetalladoExport implements FromView
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
        return view('remision.excel.reporte-cliente-detallado', [
            'fecha' => Carbon::now(),
            'inicio' => $this->inicio,
            'final' => $this->final,
            'remisiones' => $this->get_remisiones()
        ]);
    }

    public function get_remisiones(){
        if($this->inicio != '0000-00-00' && $this->final != '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $this->cliente_id)
                            ->with('cliente:id,name')->with('datos.libro')
                            ->whereBetween('fecha_creacion', [$this->inicio, $this->final])
                            ->whereNotIn('estado', ['Iniciado', 'Cancelado'])
                            ->orderBy('id','desc')
                            ->get();
        }
        else {
            $remisiones = Remisione::where('cliente_id', $this->cliente_id)
                    ->with('cliente:id,name')->with('datos.libro')
                    ->whereNotIn('estado', ['Iniciado', 'Cancelado'])
                    ->orderBy('id','desc')
                    ->get();
        }
        
        return $remisiones;
    }
}
