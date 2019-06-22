<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Reportes</title>


        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
        </style>
    </head>
    <body>
        
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Numero</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Total Salida</th>
                    <th scope="col">Total Devolución</th>
                    <th scope="col">Total a pagar</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Fecha de entrega</th>
                </tr>
            </thead>
            <tbody>
                @foreach($remisiones as $remision)
                    <tr>
                        <td>{{ $remision->id }}</td>
                        <td>{{ $remision->cliente->name }}</td>
                        <td>$ {{ $remision->total }}</td>
                        <td>$ {{ $remision->total_devolucion }}</td>
                        <td>$ {{ $remision->total_pagar }}</td>
                        <td>
                            @if($remision->estado == 'Iniciado')
                                <span class="badge badge-secondary">{{ $remision->estado }}</span>
                            @endif
                            @if($remision->estado == 'Proceso')
                                <span class="badge badge-primary">{{ $remision->estado }}</span>
                                @endif
                            @if($remision->estado == 'Terminado')
                                <span class="badge badge-success">{{ $remision->estado }}</span>
                                @endif
                        </td>
                        <td>{{ $remision->fecha_entrega }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
