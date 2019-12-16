<table>
    <tr>
        <th><b>LIBRO</b></th>
        <th><b>ENTRADAS</b></th>
        <th><b>DEVOLUCIONES</b></th>
        <th><b>NOTAS (DEVOLUCIONES)</b></th>
        <th><b>REMISIONES</b></th>
        <th><b>NOTAS (SALIDA)</b></th>
        <th><b>PEDIDOS</b></th>
        <th><b>PROMOCIONES</b></th>
        <th><b>DONACIONES</b></th>
        <th><b>EXISTENCIA</b></th>
    </tr>
    @foreach($movimientos as $movimiento)
        <tr>
            <td>{{ $movimiento['libro'] }}</td>
            <td>{{ $movimiento['entradas'] }}</td>
            <td>{{ $movimiento['devoluciones'] }}</td>
            <td>{{ $movimiento['notas_entrada'] }}</td>
            <td>{{ $movimiento['remisiones'] }}</td>
            <td>{{ $movimiento['notas_salida'] }}</td>
            <td>{{ $movimiento['pedidos'] }}</td>
            <td>{{ $movimiento['promociones'] }}</td>
            <td>{{ $movimiento['donaciones'] }}</td>
            <td>{{ $movimiento['existencia'] }}</td>
        </tr>
    @endforeach
</table>