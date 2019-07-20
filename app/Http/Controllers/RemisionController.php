<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use NumerosEnLetras;
use App\Devolucione;
use Carbon\Carbon;
use App\Remisione;
use App\Cliente;
use App\Libro;
use App\Dato;
use PDF;

class RemisionController extends Controller
{
    public function registro(Request $request){
        if($request->remision_id != 0){
            $remision = $request->remision_id;
        }
        else{
            $remision = Remisione::all()->count() + 1;
        }
        
        try {

            \DB::beginTransaction();

            $dato = Dato::create([
                'remision_id' => $remision,
                'libro_id'  => $request->id,
                'costo_unitario' => $request->costo_unitario,
                'unidades'  => $request->unidades,
                'total'     => $request->total
            ]);

            $libro = Libro::whereId($dato->libro_id)->first();
            $libro->update(['piezas' => $libro->piezas - $dato->unidades]);
            
            \DB::commit();

            return response()->json(['dato' => $dato, 'libro' => $dato->libro]);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function store(Request $request){
        try {
            \DB::beginTransaction();
            $cliente = Cliente::whereId($request->cliente_id)->update(['estado' => 'Terminado']);

            $remision = Remisione::create([
                'cliente_id' => $request->cliente_id,
                'total' => $request->total,
                'fecha_entrega' => $request->fecha_entrega,
                'estado' => 1,
                'fecha_creacion' => Carbon::now()->format('Y-m-d')
            ]);

            $this->concluir_remision($remision->id);

            \DB::commit();
        
            return response()->json($remision);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function show(){
        $numero = Input::get('numero');
        $remision = Remisione::whereId($numero)->first();
        $datos = Dato::where('remision_id', $remision->id)->with('libro')->get();
        $devoluciones = Devolucione::where('remision_id', $remision->id)->with('libro', 'dato')->get();
        return response()->json(['remision' => $remision, 'datos' => $datos, 'devoluciones' => $devoluciones]);
    }

    public function actualizar(Request $request){
        $remision = Remisione::whereId($request->id)->first();
        
        try {
            \DB::beginTransaction();

            $remision->fecha_entrega = $request->fecha_entrega;
            $remision->total = $request->total;
            $remision->save();

            \DB::commit();

            $this->concluir_remision($remision->id);

            return response()->json($remision);

        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function concluir_remision($id){
        $datos = Dato::where('remision_id', $id)->where('estado', 'Eliminado')->get();

        foreach($datos as $dato){
            $libro = Libro::whereId($dato->libro_id)->first();
            $libro->update(['piezas' => $libro->piezas + $dato->unidades]);
        }

        Dato::where('remision_id', $id)->where('estado', 'Eliminado')->delete();
        Dato::where('remision_id', $id)->update(['estado' => 'Terminado']);
    }

    public function eliminar(Request $request){
        try {
            \DB::beginTransaction();

            $dato = Dato::whereId($request->id)->update(['estado' => 'Eliminado']);

            \DB::commit();
            
            return response()->json($dato);
        
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($exception->getMessage());
		}
    }

    public function todos(){
        $remisiones = Remisione::with('cliente')->get();
        return response()->json($remisiones);
    }

    public function por_numero(){
        $num_remision = Input::get('num_remision');
        $remision = Remisione::whereId($num_remision)->first();
        $cliente = Cliente::whereId($remision->cliente_id)->first();
        return response()->json(['remision' => $remision, 'cliente_nombre' => $cliente->name]);
    }

    public function por_cliente(){
        $id = Input::get('id');
        $inicio = Input::get('inicio');
        $final = Input::get('final');

        if($inicio != '' && $final != ''){
            $remisiones = Remisione::where('cliente_id', $id)->whereBetween('fecha_creacion', [$inicio, $final])->with('cliente')->get();
        }
        if($id != 0){
            $remisiones = Remisione::where('cliente_id', $id)->with('cliente')->get();
        }

        return response()->json($remisiones);
    }

    public function por_fecha(){
        $inicio = Input::get('inicio');
        $final = Input::get('final');
        $cliente_id = Input::get('cliente_id');

        if($cliente_id != 0){
            $remisiones = Remisione::where('cliente_id', $cliente_id)->whereBetween('fecha_creacion', [$inicio, $final])->with('cliente')->get();
        }
        else{
            $remisiones = Remisione::whereBetween('fecha_creacion', [$inicio, $final])->with('cliente')->get();
        }

        return response()->json($remisiones);
    }

    public function por_estado(){
        $estado = Input::get('estado');
        $remisiones = Remisione::where('estado', $estado)->with('cliente')->get();
        return response()->json($remisiones);
    }

    public function imprimirSalida($id){
        $remision = Remisione::whereId($id)->first();
        $data['remision'] = $remision;

        if($remision->estado == 'Iniciado'){
            $datos = Dato::where('remision_id', $remision->id)->with('libro')->get();
            $data['datos'] = $datos;
            $data['total_salida'] = NumerosEnLetras::convertir($remision->total);
            $pdf = PDF::loadView('remision.nota', $data);    
        }         
        if($remision->estado == 'Terminado'){
            $devoluciones = Devolucione::where('remision_id', $remision->id)->with('libro')->with('dato')->get();
            $data['devoluciones'] = $devoluciones;
            $data['total_final'] = NumerosEnLetras::convertir($remision->total_pagar);
            $pdf = PDF::loadView('remision.final', $data); 
        }
        
        return $pdf->download('remision.pdf');
    }

    public function imprimirCliente($cliente_id, $inicio, $final){
        if($cliente_id != 0 && $final == '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $cliente_id)->with('cliente')->get();
        }
        if($cliente_id == 0 && $final != '0000-00-00'){
            $remisiones = Remisione::whereBetween('fecha_creacion', [$inicio, $final])->with('cliente')->get();
        }
        if($cliente_id != 0 && $final != '0000-00-00'){
            $remisiones = Remisione::where('cliente_id', $cliente_id)->whereBetween('fecha_creacion', [$inicio, $final])->with('cliente')->get();
        }
        if($cliente_id == 0 && $final == '0000-00-00'){
            $remisiones = Remisione::with('cliente')->get();
        }
        
        $data = $this->valores($remisiones, $inicio, $final);

        $pdf = PDF::loadView('remision.reporte', $data);
        return $pdf->download('reporte.pdf');
    }

    public function imprimirEstado($estado){
        $remisiones = Remisione::where('estado', $estado)->with('cliente')->get();

        $data = $this->valores($remisiones, '0000-00-00', '0000-00-00');

        $pdf = PDF::loadView('remision.reporte', $data);
        return $pdf->download('reporte.pdf');
    }

    public function valores($remisiones, $inicio, $final){
        $total_salida = 0;
        $total_devolucion = 0;
        $total_pagar = 0;

        foreach($remisiones as $r){
            $total_salida += $r->total;
            $total_devolucion += $r->total_devolucion;
            $total_pagar += $r->total_pagar;            
        }

        $data['remisiones'] = $remisiones;
        $data['total_salida'] = $total_salida;
        $data['total_devolucion'] = $total_devolucion;
        $data['total_pagar'] = $total_pagar;
        $data['fecha_inicio'] = $inicio;
        $data['fecha_final'] = $final;

        return $data;
    }

    public function nueva(){
        $remision = Remisione::all()->count() + 1;

        try {
            \DB::beginTransaction();
            $this->comprobar($remision - 1);
            //Si una remision estab siendo creada pero no se guardo se recuperan los datos en estado Eliminado e Iniciado
            //Se deshacen los cambios de disminucion de piezas
            $eliminados = Dato::where('remision_id', $remision)->where('estado', 'Eliminado')->get();
            if($eliminados->count() > 0){
                foreach($eliminados as $eliminado){
                    $libro = Libro::whereId($eliminado->libro_id)->first();
                    $libro->update(['piezas' => $libro->piezas + $eliminado->unidades]);
                }
            }

            $datos = Dato::where('remision_id', $remision)->where('estado', 'Iniciado')->get();
            if($datos->count() > 0){
                foreach($datos as $dato){
                    $libro = Libro::whereId($dato->libro_id)->first();
                    $libro->update(['piezas' => $libro->piezas + $dato->unidades]);
                }
            }

            Dato::where('remision_id', $remision)->where('estado', 'Eliminado')->delete();
            Dato::where('remision_id', $remision)->where('estado', 'Iniciado')->delete();
            Cliente::where('estado', 'Iniciado')->delete();
            \DB::commit();

        } catch (Exception $e) {
            \DB::rollBack();
        }
        
        return response()->json(null, 200);
    }

    public function nueva_edicion(){
        $id = Input::get('id');
        $this->comprobar($id);
        return response()->json(null, 200);
    }

    public function comprobar($remision){
        //En caso de que alguna remision estaba siendo editada y se elimino un dato pero no se guardo, 
        //se cambia el estado de nuevo a Terminado
        Dato::where('remision_id', $remision)
            ->where('estado', 'Eliminado')
            ->update(['estado' => 'Terminado']);
        //En caso de haber agregado uno nuevo, se recuperan las piezas
        $noguardados = Dato::where('remision_id', $remision)->where('estado', 'Iniciado')->get();
        if($noguardados->count() > 0){
            foreach($noguardados as $noguardado){
                $libro = Libro::whereId($noguardado->libro_id)->first();
                $libro->update(['piezas' => $libro->piezas + $noguardado->unidades]);
            }
        }
        //Se eliminan dichos datos
        Dato::where('remision_id', $remision)->where('estado', 'Iniciado')->delete();
    }

    public function por_estado_libros(){
        $estado = Input::get('estado');
        if($estado == 'Iniciado'){
            $remisiones = \DB::table('libros')
            ->join('datos', 'libros.id', '=', 'datos.libro_id')
            ->select(
                'ISBN', 
                'titulo', 
                \DB::raw('SUM(datos.unidades) as unidades'),
                \DB::raw('SUM(datos.total) as total'))
            ->groupBy('ISBN', 'titulo')
            ->get();
        }
        if($estado == 'Proceso'){
            $remisiones = \DB::table('libros')
            ->join('devoluciones', 'libros.id', '=', 'devoluciones.libro_id')
            ->select(
                'ISBN', 
                'titulo', 
                \DB::raw('SUM(devoluciones.unidades) as unidades_devolucion'),
                \DB::raw('SUM(devoluciones.total) as total_devolucion'),
                \DB::raw('SUM(devoluciones.unidades_resta) as unidades_final'), 
                \DB::raw('SUM(devoluciones.total_resta) as total_final'))
            ->groupBy('ISBN', 'titulo')
            ->get();
        }
        if($estado == 'Terminado'){
            $remisiones = \DB::table('libros')
            ->join('devoluciones', 'libros.id', '=', 'devoluciones.libro_id')
            ->select(
                'ISBN', 
                'titulo', 
                \DB::raw('SUM(devoluciones.unidades_resta) as unidades_final'), 
                \DB::raw('SUM(devoluciones.total_resta) as total_final'))
            ->groupBy('ISBN', 'titulo')
            ->get();
        }
        
        return response()->json($remisiones);
    }
}
