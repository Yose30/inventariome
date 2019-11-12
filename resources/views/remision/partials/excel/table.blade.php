<table>
    <tr>
        <th>FOLIO</th>
        <th>FECHA</th> 
        <th>CLIENTE</th>
        <th>SALIDA</th>
        <th>PAGOS</th>
        <th>DEVOLUCIÓN</th>
        <th>DONACIÓN</th>
        <th>PAGAR</th>
    </tr>
    @foreach($remisiones as $remision)
        <tr>
            <td>{{ $remision->id }}</td>
            <td>{{ $remision->fecha_creacion }}</td>
            <td>{{ $remision->cliente->name }}</td>
            <td>${{ $remision->total }}</td>
            <td>${{ $remision->pagos }}</td>
            <td>${{ $remision->total_devolucion }}</td>
            <td>${{ $remision->total_donacion }}</td>
            <td>${{ $remision->total_pagar }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td><td></td>
        <td><b>TOTAL</b></td>
        <td><b>${{ $totales['total_salida'] }}</b></td>
        <td><b>${{ $totales['total_pagos'] }}</b></td>
        <td><b>${{ $totales['total_devolucion'] }}</b></td>
        <td><b>${{ $totales['total_donacion'] }}</b></td>
        <td><b>${{ $totales['total_pagar'] }}</b></td>
    </tr>
</table>