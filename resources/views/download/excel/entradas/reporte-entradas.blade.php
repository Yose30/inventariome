<!doctype html>
<html>
    <head>
        <title>Entrada</title>
    </head>
    <body>
        <div>
            <div>
                @include('download.partials.excel.fechas', ['fecha' => $fecha, 'inicio' => $inicio, 'final' => $final])
                @if($editorial != 'TODAS')
                    <label><b>Editorial: </b> {{ $editorial }}</label><br>
                @endif
                <br>
                @if($grupos !== null && $tipo === 'general')
                    <h4>GENERAL</h4>
                    <table>
                        <tr>
                            <th></th>
                            <th><b>Editorial</b></th>
                            <th><b>Unidades</b></th>
                            <th><b>Total</b></th>
                            <th><b>Pagos</b></th>
                            <th><b>Pendiente</b></th>
                        </tr>
                        @foreach($grupos as $grupo)
                            <tr>
                                <td></td>
                                <td>{{ $grupo->editorial }}</td>
                                <td>{{ $grupo->unidades }}</td>
                                <td>{{ $grupo->total }}</td>
                                <td>{{ $grupo->total_pagos }}</td>
                                <td>{{ $grupo->total_pendiente }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td><td></td>
                            <td><b>{{ $totales['total_unidades'] }}</b></td>
                            <td><b>{{ $totales['total'] }}</b></td>
                            <td><b>{{ $totales['total_pagos'] }}</b></td>
                            <td><b>{{ $totales['total_pendiente'] }}</b></td>
                        </tr>
                    </table>
                    <br><br>
                    <h4>DETALLADO</h4>
                @endif
                @if($tipo === 'general')
                    @include('download.excel.entradas.general', ['entradas' => $entradas, 'totales' => $totales])
                @else 
                    @include('download.excel.entradas.detallado', ['entradas' => $entradas, 'totales' => $totales])
                @endif
            </div>
        </div>
    </body>
</html>
