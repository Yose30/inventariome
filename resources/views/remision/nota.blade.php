<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nota de remisión</title>


        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <!-- Styles -->
        <style>
            @page {
                margin: 160px 50px;
                }
            html, body {
                background-color: #fff;
                color: #000000;
                font-family:'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
            h1{
                font-size:18px;
            }
            p{
                font-size:10px;
                line-height:1.1;
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
            
           
            .sinBorde{
                border-width: 0px;
            }
            .bordesVer
            {
                border-left:1px solid #ddd;
                border-right:1px solid #ddd;
                
            }
            .bordesVerTot
            {
                
                border-right:1px solid #ddd;
               
            }
            
            #qr{
                align:center;
            }
            #contacto{
                text-align:center;
                font-weight: bold;

            }
            #DFM{
                text-align:center;
                font-weight: bold;
            }
            #tdder{
                text-align:right;
            }
            #tdcent{
                text-align:center;
            }
            #folio{
                text-align:center;
                color:red;
                font-size:14px;
            }
            #total{
                text-align:right;
                font-weight: bold;
                font-size:14px;


            }


            footer {
                color:black;
                position: fixed;
                left: 100px;
                bottom: 0px;
                right: 0px;
                height: 100px;
                border-bottom: 0px solid #ddd;
                }
                footer .page:after {
                content: counter(page);
                }
                footer table {
                width: 100%;
                }
                footer p {
                text-align: center;
                }
                footer .izq {
                text-align: center;
                }
           
        </style>
    </head>
    <body>
        <div class="container">
            <main class="py-4">

                <div align="center">
                <table cellpadding="100">
                    <tr>
                        <!--<img src="{{ asset('img/Globo.png')}}" height="100" width="100" align="right" ></th> -->
                        <th class="sinBorde"></th>
                        <th class="sinBorde"><h1>Omega Book Company S. A. de C. V.</h1></th>
                        <th class="sinBorde"></th> 
                        <th class="sinBorde"></th>
                    </tr>
                    <tr>
                        <td class="sinBorde"></td>
                        <td class="sinBorde" id="DFM">Distribuidor for Mexico</td>
                        <td class="sinBorde" ></td>
                        <td class="sinBorde" id="contacto">Folio</td>
                    </tr>

                    <tr>
                        <td class="sinBorde"></td>
                        <td class="sinBorde"><br></td>
                        <td class="sinBorde"></td>
                        <td class="sinBorde" id="folio">{{$remision -> id}}</td>
                    </tr>
                    <tr>
                        <td class="sinBorde"></td>
                        <td class="sinBorde"></td>
                        <td class="sinBorde" style="text-align:right"> <p>Fecha </p> </td>
                        <td class="sinBorde" id="tdder">
                            <p align="center">{{ $remision->fecha_entrega }}</p>
                        </td>
                    </tr>
                </table>
             

                <table style="width:100%" >
                    <tr>
                        <th  style="width:33%">Cliente:</th>
                        <th  style="width:33%">Forma de pago:</th> 
                        <th  style="width:34%">Dirección:</th>
                    </tr>
                    <tr>
                        <td  rowspan="3">{{ $remision->cliente->name }}</td>
                        <td >
                            <p>{{ $remision->cliente->condiciones_pago }}</p>
                        </td>
                        <td  rowspan="3">  
                            <p>{{ $remision->cliente->direccion }}</p>
                        </td>
                    </tr>

                    <tr>
                       
                        <td  style="width:33%" id="contacto">Contacto:</td>
                       
                    </tr>
                    <tr>
                        
                        <td >
                            <b>Teléfono</b>: {{ $remision->cliente->telefono }}<br>
                            <b>E-mail</b>: {{ $remision->cliente->email }}
                        </td> 
                        
                    
                    </tr>
                </table>

                <br>

                <table style="width:100%">
                    <tr>
                        <th >ISBN</th>
                        <th >Libro</th> 
                        <th >Unidades</th>
                        <th >Costo Unitario</th>
                        <th >Subtotal</th>
                    </tr>
                    @foreach($datos as $dato)
                    <tr>

                        <td class="bordesVer" style="width:20%">{{ $dato->isbn_libro }}</td>
                        <td class="bordesVer" style="width:25%">{{ $dato->titulo }}</td> 
                        <td class="bordesVer" style="width:15%" id="tdcent">{{ $dato->unidades }}</td>
                        <td class="bordesVer" style="width:20%"  id="tdder">{{ $dato->costo_unitario }}</td>
                        <td class="bordesVer" style="width:20%"  id="tdder">{{ $dato->total }}</td>

                    </tr>
                    @endforeach
                   

                    <tr>
                        <td class="sinBorde"class="sinBorde" colspan="2" rowspan="2" id="qr" align="center"><!--<img src="{{ asset('img/QR.jpg')}}" height="150px" width="300px"  >--></td>
                        <td class="bordesVerTot"colspan="2"  id="total">TOTAL:</td>
                        <td class="bordesVer"id="total">{{$remision -> total}}</td>
                    </tr>
                    
                    <tr>
                        <td class="sinBorde" id="total" colspan="3">Nuve millones trecientos sesenta y cinco mil docientos cincuenta y cuatro pesos mx</td>
                               
                    </tr>
                    
                </table>
                

                <footer>
                    <table>
                    <tr>
                        <td>
                            <p class="izq">
                            <p> Av. del Taller # 460, Col. Jardín Balbuena C. P. 15900 <br/>Del. Venustiano Carranza, Ciudad de México.<br/>Tel: 55-5803-64-15        mail: tere.omega1@hotmail.com</p>
                            </p>
                        </td>
                    </tr>
                    </table>
                </footer>
                
                </div>
            </main>
        </div>
    </body>
</html>
