<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
                    <h5><b>Cliente:</b> {{ $cliente }}</h5>
                    @foreach($remisiones as $remision)
                        <label><b>Remisión No.{{ $remision['id'] }}</b></label><br>
                        <label><b>Fecha de creación: {{ $remision['fecha_creacion'] }}</b></label><br>
                        @if($remision['estado'] == 'Iniciado')
                            <b>No entregado</b>
                        @endif
                        @if($remision['total_pagar'] == 0 && $remision['pagos'] > 0)
                            <b>Pagado</b>
                        @endif
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
                                <td style="width:20%">{{$dato['libro']['ISBN']}}</td>
                                <td style="width:25%">{{$dato['libro']['titulo']}}</td>
                                <td style="width:20%" id="tdder">${{$dato['costo_unitario']}}</td>
                                <td style="width:15%" id="tdcent">{{$dato['unidades']}}</td>
                                <td style="width:20%" id="tdder">${{$dato['total']}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="sinBorde"></td><td class="sinBorde"></td><td class="sinBorde"></td>
                                <td  class="sinBorde" id="tdder"><b>SALIDA</b></td>
                                <td  class="sinBorde" id="tdder"><b>${{$remision['total']}}</b></td>
                            </tr>
                            <tr>
                                <td class="sinBorde"></td><td class="sinBorde"></td><td class="sinBorde"></td>
                                <td class="sinBorde" id="tdder"><b>PAGOS</b></td>
                                <td class="sinBorde" id="tdder"><b>${{$remision['pagos']}}</b></td>
                            </tr>
                            <tr>
                                <td class="sinBorde"></td><td class="sinBorde"></td><td class="sinBorde"></td>
                                <td class="sinBorde" id="tdder"><b>DEVOLUCIÓN</b></td>
                                <td class="sinBorde" id="tdder"><b>${{$remision['total_devolucion']}}</b></td>
                            </tr>
                            <tr>
                                <td class="sinBorde"></td><td class="sinBorde"></td><td class="sinBorde"></td>
                                <td class="sinBorde" id="tdder"><b>DONACIÓN</b></td>
                                <td class="sinBorde" id="tdder"><b>${{$remision['total_donacion']}}</b></td>
                            </tr>
                            <tr>
                                <td class="sinBorde"></td><td class="sinBorde"></td><td class="sinBorde"></td>
                                <td class="sinBorde" id="tdder"><b>PAGAR</b></td>
                                <td class="sinBorde" id="tdder"><b>${{$remision['total_pagar']}}</b></td>
                            </tr>
                        </table>
                        <hr>
                    @endforeach
                </div>
            </main>
        </div>
    </body>
</html>
