<!doctype html>
<html>
    <head>
        <title>Reporte</title>
    </head>
    <body>
        <div>
            <div>
                <h5><b>Fecha:</b> {{ $fecha }}</h5>
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
                @if($inicio != '0000-00-00' && $final != '0000-00-00')
                    <h6><b>De:</b> {{ $inicio }} - <b>A:</b> {{ $final }}</h6>
                @endif
                @include('remision.partials.excel.table', ['remisiones' => $remisiones, 'totales' => $totales])
            </div>
        </div>
    </body>
</html>
