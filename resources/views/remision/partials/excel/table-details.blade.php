@foreach($remisiones as $remision)
    <h5><b>Cliente:</b> {{ $remision->cliente->name }}</h5>
    <label><b>Remisión No.{{ $remision['id'] }}</b></label><br>
    <label><b>Fecha de creación: {{ $remision['fecha_creacion'] }}</b></label><br>
    <br>
    <table>
        <tr>
            <th>ISBN</th>
            <th>TITULO</th> 
            <th>COSTO UNITARIO</th>
            <th>UNIDADES</th>
            <th>SUBTOTAL</th>
        </tr>
        @foreach($remision['datos'] as $dato)
        <tr>
            <td>{{$dato['libro']['ISBN']}}</td>
            <td>{{$dato['libro']['titulo']}}</td>
            <td>${{$dato['costo_unitario']}}</td>
            <td>{{$dato['unidades']}}</td>
            <td>${{$dato['total']}}</td>
        </tr>
        @endforeach
        <tr>
            <td></td><td></td><td></td>
            <td ><b>SALIDA</b></td>
            <td ><b>${{$remision['total']}}</b></td>
        </tr>
        <tr>
            <td></td><td></td><td></td>
            <td><b>PAGOS</b></td>
            <td><b>${{$remision['pagos']}}</b></td>
        </tr>
        <tr>
            <td></td><td></td><td></td>
            <td><b>DEVOLUCIÓN</b></td>
            <td><b>${{$remision['total_devolucion']}}</b></td>
        </tr>
        <tr>
            <td></td><td></td><td></td>
            <td><b>DONACIÓN</b></td>
            <td><b>${{$remision['total_donacion']}}</b></td>
        </tr>
        <tr>
            <td></td><td></td><td></td>
            <td><b>PAGAR</b></td>
            <td><b>${{$remision['total_pagar']}}</b></td>
        </tr>
    </table>
@endforeach