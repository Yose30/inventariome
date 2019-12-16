<?php

namespace App\Http\Controllers;

use App\Exports\DonacionesExport;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Donacione;
use Carbon\Carbon;
use App\Regalo;
use App\Libro;
use Excel;
use PDF;

class DonacioneController extends Controller
{
    // GUARDAR UNA DONACIÓN
    // Función utilizada en DevoluciónController
    public function store(Request $request) {
        try{
            \DB::beginTransaction();
            $regalo = Regalo::create([
                'plantel' => strtoupper($request->plantel),
                'descripcion' => strtoupper($request->descripcion),
                'unidades' => (int) $request->unidades,
                'entregado_por' => null
            ]);
            
            foreach($request->donaciones as $donacion){
                $unidades = $donacion['unidades'];
                $libro_id = $donacion['id'];

                // Crear registro de donación
                Donacione::create([
                    'regalo_id' => $regalo->id,
                    'libro_id' => $libro_id,
                    'unidades' => $unidades
                ]);

                $libro = Libro::whereId($libro_id)->first();
                $libro->update(['piezas' => $libro->piezas - $unidades]);
            }
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
        }
        
        return response()->json($regalo);
    }

    public function detalles_donacion(){
        $regalo_id = Input::get('regalo_id');
        $regalo = Regalo::whereId($regalo_id)->with('donaciones.libro')->first();
        return response()->json($regalo);
    }

    public function buscar_plantel_regalo(){
        $queryPlantel = Input::get('queryPlantel');
        $regalos = Regalo::where('plantel','like','%'.$queryPlantel.'%')->orderBy('id','desc')->get();
        return response()->json($regalos);
    }

    public function buscar_fecha_regalo(){
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        $plantel = Input::get('plantel');

        $fechas = $this->format_date($inicio, $final);
        $fecha1 = $fechas['inicio'];
        $fecha2 = $fechas['final'];

        if($plantel === null){
            $regalos = Regalo::whereBetween('created_at', [$fecha1, $fecha2])
                                ->orderBy('id','desc')->get();
        } else {
            $regalos = Regalo::where('plantel','like','%'.$plantel.'%')
                                ->whereBetween('created_at', [$fecha1, $fecha2])
                                ->orderBy('id','desc')->get();
        }
        
        return response()->json($regalos);
    }

    public function format_date($fecha1, $fecha2){
        $inicio = new Carbon($fecha1);
        $final 	= new Carbon($fecha2);
        $inicio = $inicio->format('Y-m-d 00:00:00');
        $final 	= $final->format('Y-m-d 23:59:59');

        $fechas = [
            'inicio' => $inicio,
            'final' => $final
        ];

        return $fechas;
    }

    public function download_donacion($plantel, $inicio, $final, $tipo){
        return Excel::download(new DonacionesExport($plantel, $inicio, $final, $tipo), 'reporte-donaciones.xlsx');
    }

    public function entrega_donacion(Request $request){
        $regalo = Regalo::whereId($request->id)->first();
        try {
            \DB::beginTransaction();
                $regalo->update(['entregado_por' => $request->entregado_por]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
        }
        return response()->json($regalo);
    }

    public function download_regalo($id){
        $regalo = Regalo::whereId($id)->with('donaciones.libro')->first();
        $data['regalo'] = $regalo;
        $pdf = PDF::loadView('download.pdf.donaciones.donacion', $data); 
        return $pdf->download('donacion.pdf');
    }
}
