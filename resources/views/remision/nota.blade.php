<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nota de remisión</title>


        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #000000;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
            .card .card-body{
                border-style: solid;
                border-width: 1px;
                height: 200px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <main class="py-4">
                <div align="right">
                    <h5>Fecha de entrega</h5>
                    <p>{{ $remision->fecha_entrega }}</p>
                </div>
                <div class="card mb-3">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <div class="card-body">
                                <h6 class="card-title">Cliente</h6>
                                <p class="card-text">{{ $remision->cliente->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-body">
                                <h6 class="card-title">Condiciones de pago</h6>
                                {{ $remision->cliente->condiciones_pago }}<br>
                                <h6 class="card-title">Contacto</h6>
                                <b>Teléfono</b>: {{ $remision->cliente->telefono }}<br>
                                <b>E-mail</b>: {{ $remision->cliente->email }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-body">
                                <h6 class="card-title">Dirección de entrega</h6>
                                <p class="card-text">{{ $remision->cliente->direccion }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
