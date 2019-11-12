<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Remisione;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EstadoDetalladoExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($estado, $cliente_id, $inicio, $final)
    {
        $this->estado = $estado;
        $this->cliente_id = $cliente_id;
        $this->inicio = $inicio;
        $this->final = $final;
    }

    public function view(): View
    {
        $remisiones = $this->get_remisiones();
        return view('remision.excel.reporte-estado-detallado', [
            'remisiones' => $remisiones, 
            'estado' => $this->estado,
            'inicio' => $this->inicio,
            'final' => $this->final,
            'fecha' => Carbon::now()
        ]);
    }

    public function get_remisiones(){
        if($this->estado === 'cancelado'){
            if($this->final != '0000-00-00'){ $remisiones = $this->estadoS_CF(4, $this->inicio, $this->final); }
            else { 
                if($this->cliente_id === 'null'){ $remisiones = $this->estadoS_SF(4, null); }
                else { $remisiones = $this->estadoS_SF(4, $this->cliente_id); } 
            }
        }
        if($this->estado === 'no_entregado'){
            if($this->final != '0000-00-00'){ $remisiones = $this->estadoS_CF(1, $this->inicio, $this->final); }
            else { 
                if($this->cliente_id === 'null'){ $remisiones = $this->estadoS_SF(1, null); }
                else { $remisiones = $this->estadoS_SF(1, $this->cliente_id); }
            }
        }
        if($this->estado === 'entregado'){
            if($this->final != '0000-00-00'){ $remisiones = $this->estadoS_CF(2, $this->inicio, $this->final); }
            else {
                if($this->cliente_id === 'null'){ $remisiones = $this->estadoS_SF(2, null); }
                else { $remisiones = $this->estadoS_SF(2, $this->cliente_id); }
            }
        }
        if($this->estado === 'pagado'){
            if($this->final != '0000-00-00'){ $remisiones = $this->pagado_CF($this->inicio, $this->final); }
            else {
                if($this->cliente_id === 'null'){ $remisiones = $this->pagado_SF(null);  }
                else { $remisiones = $this->pagado_SF($this->cliente_id);  }
                
            }
        }
        return $remisiones;
    }

    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO CON FECHA
    public function estadoS_CF($estado, $inicio, $final){
        if ($estado == 1 || $estado == 4) {
            return Remisione::where('estado',$estado)
                ->whereBetween('fecha_creacion', [$inicio, $final])
                ->orderBy('cliente_id','asc')
                ->with('cliente:id,name')->with('datos.libro')->get();
        }
        if ($estado == 2){
            return Remisione::where('estado',$estado)->where('total_pagar', '>', 0)
                ->whereBetween('fecha_creacion', [$inicio, $final])
                ->orderBy('cliente_id','asc')
                ->with('cliente:id,name')->with('datos.libro')->get();
        }
    }
    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO SIN FECHA
    public function estadoS_SF($estado, $cliente_id){
        if ($estado == 1 || $estado == 4) {
            if($cliente_id === null){
                return Remisione::where('estado',$estado)
                    ->orderBy('cliente_id','asc')
                    ->with('cliente:id,name')
                    ->with('datos.libro')->get();
            } else {
                return Remisione::where('estado',$estado)
                            ->where('cliente_id', $cliente_id)
                            ->orderBy('cliente_id','asc')
                            ->with('cliente:id,name')->with('datos.libro')->get();
            }
        }
        if ($estado == 2){
            if($cliente_id === null){
                return Remisione::where('estado',$estado)->where('total_pagar', '>', 0)
                    ->orderBy('cliente_id','asc')
                    ->with('cliente:id,name')->with('datos.libro')->get();
            } else {
                return Remisione::where('estado',$estado)
                    ->where('cliente_id', $cliente_id)
                    ->where('total_pagar', '>', 0)
                    ->orderBy('cliente_id','asc')
                    ->with('cliente:id,name')->with('datos.libro')->get();
            }
        }
    }

    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO (PAGADO) CON FECHA
    public function pagado_CF($inicio, $final){
        return Remisione::where('total_pagar', '=', 0)
                        ->where(function ($query) {
                            $query->where('pagos', '>', 0)
                                    ->orWhere('total_devolucion', '>', 0);
                        })
                        ->whereBetween('fecha_creacion', [$inicio, $final])
                        ->orderBy('cliente_id','asc')
                        ->with('cliente:id,name')->with('datos.libro')->get();
    }

    // FUNCIÓN PARA LA BUSQUEDA DE REMISIÓN POR ESTADO (PAGADO) SIN FECHA
    public function pagado_SF($cliente_id){
        if($cliente_id === null){
            return Remisione::where('total_pagar', '=', 0)
                        ->where(function ($query) {
                            $query->where('pagos', '>', 0)
                                    ->orWhere('total_devolucion', '>', 0);
                        })
                        ->orderBy('cliente_id','asc')
                        ->with('cliente:id,name')->with('datos.libro')->get();
        } else {
            return Remisione::where('cliente_id', $cliente_id)
                        ->where('total_pagar', '=', 0)
                        ->where(function ($query) {
                            $query->where('pagos', '>', 0)
                                    ->orWhere('total_devolucion', '>', 0);
                        })
                        ->orderBy('cliente_id','asc')
                        ->with('cliente:id,name')->with('datos.libro')->get();
        }
    }
}
