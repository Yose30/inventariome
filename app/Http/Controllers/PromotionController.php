<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Promotion;
use App\Departure;
use App\Libro;

class PromotionController extends Controller
{
    // MOSTRAR PROMOCIONES POR FOLIO
    // Función utilizada en PromocionesComponent
    public function buscar_folio(){
        $folio = Input::get('folio');
        $promotion = Promotion::where('folio', $folio)->first();
        return response()->json($promotion);
    }

    // MOSTRAR PROMOCIONES POR PLANTEL
    // Función utilizada en PromocionesComponent
    public function buscar_plantel(){
        $queryPlantel = Input::get('queryPlantel');
        $promotions = Promotion::where('plantel','like','%'.$queryPlantel.'%')->orderBy('folio','desc')->get();
        return response()->json($promotions);
    }

    // MOSTRAR LOS DETALLES DE UNA PROMOCIÓN
    // Función utilizada en PromocionesComponent
    public function obtener_departures(){
        $promotion_id = Input::get('promotion_id');
        $promotion = Promotion::whereId($promotion_id)->with('departures.libro')->first();
        // $departures = Departure::where('promotion_id', $promotion_id)->with('libro')->get();
        return response()->json($promotion);
    }

    // GUARDAR UNA PROMOCIÓN
    // Función utilizada en PromocionesComponent
    public function store(Request $request){
        try{
            \DB::beginTransaction();
            $num = Promotion::get()->count() + 1;
            if($num < 10){
                $folio = 'A-P000'.$num;
            }
            if($num >= 10 && $num < 100){
                $folio = 'A-P00'.$num;
            }
            if($num >= 100 && $num < 1000){
                $folio = 'A-P0'.$num;
            }
            if($num >= 1000 && $num < 10000){
                $folio = 'A-P'.$num;
            }
            $promotion = Promotion::create([
                'folio' => $folio,
                'plantel' => $request->plantel,
                'descripcion' => $request->descripcion,
            ]);

            $unidades = 0;
            foreach($request->departures as $departure){
                Departure::create([
                    'promotion_id' => $promotion->id,
                    'libro_id' => $departure['id'],
                    'unidades' => $departure['unidades']
                ]);
                $libro = Libro::whereId($departure['id'])->first();
                $libro->update(['piezas' => $libro->piezas - $departure['unidades']]);
                $unidades += $departure['unidades'];
            }
            $promotion->update(['unidades' => $unidades]);
            \DB::commit();
        } catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json($promotion);
    }
}
