<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nota de remisión</title>


        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <!-- Styles -->
        <style>
            @import url('https://fonts.googleapis.com/css?family=Montserrat&display=swap');
            html, body {
                background-color: #fff;
                color: #000000;
                font-family: 'Montserrat', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }
            h1{
                font-size:32px;
            }
            p{
                line-height:1.1;
            }
            .card .card-body{
                border-style: solid;
                border-width: 1px;
                height: 200px;
            }
            table{
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                padding: 8px;
                border-bottom: 1px solid #ddd;
            }
            th{
                text-align:center;
                padding: 15px;
            }
            td{
                text-align:left;
                padding: 15px;

            }
            #qr{
                align:center;
            }
            #contacto{
                text-align:center;
                font-weight: bold;

            }
            #DFM{
                padding: 0px;
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
                font-size:18px;
            }
            #total{
                text-align:right;
                font-weight: bold;
                font-size:24px;


            }

        </style>
    </head>
    <body>
        <div class="container">
            <main class="py-4">

                <div align="center">
                <table>
                    <tr>
<<<<<<< HEAD
                        <!-- <th><img src="{{ asset('img/Globo.png')}}" height="100" width="100" align="right"></th> -->
                        <th><h1 align="center">Omega Book Company S. A. de C. V.</h1></th>
=======
                        <th><img src="{{ asset('img/Globo.png')}}" height="100" width="100" align="right" ></th>
                        <th><h1 >Omega Book Company S. A. de C. V.</h1></th>
>>>>>>> 5cac92a5f7ce1b389b2090daa4b3fa7ef0bbe4c9
                        <th></th> 
                        <th></th>
                    </tr>
                    <tr>
                        <td></td>
                        <td id="DFM">Distribuidor for Mexico</td>
                        <td ></td>
                        <td id="contacto">Folio</td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><br></td>
                        <td></td>
                        <td id="folio">{{$remision -> id}}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="width:22%" id="tdder">Fecha:</td> 
                        <td >
                            <p>{{ $remision->fecha_entrega }}</p>
                        </td>
                    </tr>
                </table>
                <br/>

                <table style="width:100%" >
                    <tr>
                        <th style="width:33%">Cliente:</th>
                        <th style="width:33%">Forma de pago:</th> 
                        <th style="width:34%">Dirección:</th>
                    </tr>
                    <tr>
<<<<<<< HEAD
                        <td></td>
                        <td><p>{{ $remision->cliente->condiciones_pago }}</p>
                        </td>
                        <td>  <p>{{ $remision->cliente->direccion }}</p></td>
=======
                        <td rowspan="3">{{ $remision->cliente->name }}</td>
                        <td>
                            <p>{{ $remision->cliente->condiciones_pago }}</p>
                        </td>
                        <td rowspan="3">  
                            <p>{{ $remision->cliente->direccion }}</p>
                        </td>
>>>>>>> 5cac92a5f7ce1b389b2090daa4b3fa7ef0bbe4c9
                    </tr>

                    <tr>
                       
                        <td style="width:33%" id="contacto">Contacto:</td>
                       
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
                        <th>ISBN</th>
                        <th>Libro</th> 
                        <th>Unidades</th>
                        <th>Costo Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                    @foreach($datos as $dato)
                    <tr>

                        <td style="width:20%">{{ $dato->isbn_libro }}</td>
                        <td style="width:25%">{{ $dato->titulo }}</td> 
                        <td style="width:15%" id="tdcent">{{ $dato->unidades }}</td>
                        <td style="width:20%"  id="tdder">{{ $dato->costo_unitario }}</td>
                        <td style="width:20%"  id="tdder">{{ $dato->total }}</td>

                    </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td> 
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="2" rowspan="7" id="qr" align="center"><img src="{{ asset('img/QR.jpg')}}" height="150px" width="300px"  ></td>
                        <td colspan="2"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="2"  id="total">TOTAL:</td>
                        <td id="total">{{$remision -> total}}</td>
                    </tr>
                    <tr>
                        <td colspan="3">Nuve millones trecientos sesenta y cinco mil docientos cincuenta y cuatro pesos mx</td>
                    </tr>
                </table>
                <table>
                <tr>
                <td colspan="5">
                    <p >Av. del Taller # 460, Col. Jardín Balbuena C. P. 15900 <br/>Del. Venustiano Carranza, Ciudad de México.<br/>Tel: 55-5803-64-15        mail: tere.omega1@hotmail.com</p>
                    <p ></p>
                    <p></p>
                </td>        
                </tr>

                </table>
                <br>
                   
                </div>
               
            </main>
        </div>
    </body>
</html>
