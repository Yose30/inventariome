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
            <td style="width:5%" id="tdder">{{ $remision->id }}</td>
            <td style="width:15%" id="tdcent">{{ $remision->fecha_creacion }}</td>
            <td style="width:30%">{{ $remision->cliente->name }}</td>
            <td style="width:10%" id="tdder">${{ $remision->total }}</td>
            <td style="width:10%" id="tdder">${{ $remision->pagos }}</td>
            <td style="width:10%" id="tdder">${{ $remision->total_devolucion }}</td>
            <td style="width:10%" id="tdder">${{ $remision->total_donacion }}</td>
            <td style="width:10%" id="tdder">${{ $remision->total_pagar }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td><td></td>
        <td id="tdder"><b>TOTAL</b></td>
        <td id="tdder"><b>${{ $totales['total_salida'] }}</b></td>
        <td id="tdder"><b>${{ $totales['total_pagos'] }}</b></td>
        <td id="tdder"><b>${{ $totales['total_devolucion'] }}</b></td>
        <td id="tdder"><b>${{ $totales['total_donacion'] }}</b></td>
        <td id="tdder"><b>${{ $totales['total_pagar'] }}</b></td>
    </tr>
</table>