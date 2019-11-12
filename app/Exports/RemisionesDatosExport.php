<?php

namespace App\Exports;

use DB;
use App\Remisione;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RemisionesDatosExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function view(): View
    {
        return view('remision.reporteprueba', [
            'remisiones' => Remisione::with('cliente:id,name')->with('datos.libro')->get()
        ]);
    }
}
