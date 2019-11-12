<!doctype html>
<html>
    <head>
        <title>Reporte</title>
    </head>
    <body>
        <div>
            <div>
                <h5><b>Fecha:</b> {{ $fecha }}</h5>
                @if($inicio != '0000-00-00' && $final != '0000-00-00')
                    <h6><b>De:</b> {{ $inicio }} - <b>A:</b> {{ $final }}</h6>
                @endif
                @include('remision.partials.excel.table', ['remisiones' => $remisiones, 'totales' => $totales])
            </div>
        </div>
    </body>
</html>
