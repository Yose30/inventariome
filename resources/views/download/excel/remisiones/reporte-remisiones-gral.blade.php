<!doctype html>
<html>
    <head>
        <title>Reporte</title>
    </head>
    <body>
        <div>
            @include('download.partials.excel.fechas', ['fecha' => $fecha, 'inicio' => $inicio, 'final' => $final])
            @if($estado === 'cancelado')
                <h4>Remisiones CANCELADAS</h4>
            @endif
            @if($estado === 'no_entregado')
                <h4>Remisiones NO ENTREGADAS</h4>
            @endif
            @if($estado === 'entregado')
                <h4>Remisiones ENTREGADAS</h4>
            @endif
            @if($estado === 'pagado')
                <h4>Remisiones PAGADAS</h4>
            @endif
            <br>
            @include('download.partials.excel.table', ['remisiones' => $remisiones, 'totales' => $totales])
            @if($estado === 'null' && $cliente_id === 'null')
                <br><br>
                <table>
                    <tr>
                        <th></th><th></th><th></th>
                        <th><b>CLIENTE</b></th>
                        <th><b>SALIDA</b></th>
                        <th><b>PAGOS</b></th>
                        <th><b>DEVOLUCIÓN</b></th>
                        <th><b>PAGAR</b></th>
                    </tr>
                    @foreach($datos as $dato)
                        <tr>
                            <td></td><td></td><th></th>
                            <td>{{ $dato->nombre }}</td>
                            <td>{{ $dato->total }}</td>
                            <td>{{ $dato->pagos }}</td>
                            <td>{{ $dato->total_devolucion }}</td>
                            <td>{{ $dato->total_pagar }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </body>
</html>
