<!doctype html>
<html>
    <head>
        <title>Reporte</title>
    </head>
    <body>
        <div>
            @if($tipo === 'unidades')
                @include('download.excel.libros.unidades', ['movimientos' => $movimientos])
            @endif
            @if($tipo === 'monto')
                @include('download.excel.libros.monto', ['movimientos' => $movimientos])
            @endif
        </div>
    </body>
</html>
