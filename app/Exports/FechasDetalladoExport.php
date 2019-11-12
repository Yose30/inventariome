<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Remisione;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FechasDetalladoExport implements FromView
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
        return view('remision.excel.reporte-fecha-detallado', [
            'remisiones' => $remisiones,
            'inicio' => $this->inicio,
            'final' => $this->final,
            'fecha' => Carbon::now()
        ]);
    }

    // RETORNAR LA BUSQUEDA DE REMISIONES POR FECHA
    public function remisiones_PF($inicio, $final){
        return Remisione::whereBetween('fecha_creacion', [$inicio, $final])
                            ->orderBy('cliente_id','asc')
                            ->with('cliente:id,name')->with('datos.libro')->get();
    }
}
