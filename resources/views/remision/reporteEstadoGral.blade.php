<!doctype html>
<html>
    <head>
        <!-- <meta charset="utf-8"> -->
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
        <title>Reporte</title>
        <style>
            label, h5 {
                font-size:13px;
            }
            th{
                border-collapse: collapse;
                text-align:center;
                font-size:12px;
            }
            td{
                border-collapse: collapse;
                text-align:left;
                font-size:10px;
            }
            table{
                border-collapse: collapse;
                width: 100%;   
            }
            th, td {
                border-collapse: collapse;
                padding: 1px;
                border-left:1px solid #ddd;
                border-right:1px solid #ddd;
                border-top:1px solid #ddd;
                border-bottom:1px solid #ddd;
            }
            #tdcent{
                text-align:center;
            }
            #tdder{
                text-align:right;
            }
            .sinBorde{
                border-width: 0px;
            }
        </style>
    </head>
    <body>
        <div>
            <main>
                <div>
                    <h4>Reporte general</h4>
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
                    </table>
                </div>
            </main>
        </div>
    </body>
</html>
