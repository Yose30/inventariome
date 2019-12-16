<table>
    <tr>
        <th><b>Folio</b></th>
        <th><b>Editorial</b></th>
        <th><b>Unidades</b></th>
        <th><b>Total</b></th>
        <th><b>Pagos</b></th>
        <th><b>Pendiente</b></th>
        <th><b>Fecha de creación</b></th>
    </tr>
    @foreach($entradas as $entrada)
        <tr>
            <td>{{ $entrada->folio }}</td> 
            <td>{{ $entrada->editorial }}</td>
            <td>{{ $entrada->unidades }}</td>
            <td>{{ $entrada->total }}</td>
            <td>{{ $entrada->total_pagos }}</td>
            <td>{{ $entrada->total - $entrada->total_pagos }}</td>
            <td>{{ $entrada->created_at->format('Y-m-d') }}</td>
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