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
            table, th, td {
                border: 1px solid black;
                }
        </style>
    </head>
    <body>
        <div class="container">
            <main class="py-4">

                <div align="center">
                <table style="width:100%" >
                    <tr>
                        <th><img src="{{ asset('img/Globo.png')}}" height="100" width="100" align="right" ></th>
                        <th><h1 align="center">Omega Book Company S. A. de C. V.</h1></th>
                        <th></th> 
                        <th><p align="center">Folio<p></th>
                    </tr>
                    <tr>
                        <td></td>
                        <td align="center">Distribuidor for Mexico</td>
                        <td ></td>
                        <td align="center">Mi folio</td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><br></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style="width:22%"><p align="right">Fecha:</p></td> 
                        <td ><p align="center">{{ $remision->fecha_entrega }}</p></td>
                    </tr>
                </table>
                <br/>

                <table style="width:100%" >
                    <tr>
                        <th style="width:33%"><p>Cliente:</p></th>
                        <th style="width:33%"><p>Forma de pago:</p></th> 
                        <th style="width:34%"><p>Dirección:</p></th>
                    </tr>
                    <tr>
                        <td rowspan="3"></td>
                        <td><p>{{ $remision->cliente->condiciones_pago }}</p>
                        </td>
                        <td rowspan="3">  <p>{{ $remision->cliente->direccion }}</p></td>
                    </tr>

                    <tr>
                       
                        <td style="width:33%"><p>Contacto:</p> 
                        </td>
                       
                    </tr>
                    <tr>
                        
                        <td ><b>Teléfono</b>: {{ $remision->cliente->telefono }}<br>
                        <b>E-mail</b>: {{ $remision->cliente->email }}</td> 
                        
                    
                    </tr>
                </table>

                <br>

                <table style="width:100%">
                    <tr>
                        <th><p align="center">ISBN</p></th>
                        <th><p align="center">Libro</p></th> 
                        <th><p align="center">Unidades</p></th>
                        <th><p align="center">Costo Unitario</p></th>
                        <th><p align="center">Subtotal</p></th>
                    </tr>
                    <tr>
                        <td style="width:20%"><p align="center">978-607-510-044-9</p></td>
                        <td style="width:25%"><p align="left">Interpretación de normas de Convivencia Social</p></td> 
                        <td style="width:15%"><p align="center">254 pzs</p></td>
                        <td style="width:20%"><p align="right">$1,784.00</p></td>
                        <td style="width:20%"><p align="right">$9,365,254.00</p></td>

                    </tr>
                    <tr>
                        <td></td>
                        <td></td> 
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="2" rowspan="7"></td>
                         
                        
                        <td colspan="2"><p align="right">Subtotal:</p></td>
                        <td><p align="right">$9,365,254.00</p></td>
                    </tr>
                    <tr>
                        
                        <td colspan="2"><p align="right">IVA:</p></td>
                        <td><p align="right">$6,784.00</p></td>
                    </tr>

                    <tr>
                        
                        <td colspan="2"><p align="right">TOTAL:</p></td>
                        <td><p align="right">$9,365,254.00</p></td>
                    </tr>
                    <tr>
                        
                        
                        <td colspan="3"><p align="right">Nuve millones trecientos sesenta y cinco mil docientos cincuenta y cuatro pesos mx</p></td>
                    </tr>
                </table>
                <tr>
                <td colspan="5"><p align="center">Av. del Taller # 460, Col. Jardín Balbuena
C. P. 15900 Del. Venustiano Carranza, Ciudad de México.
Tel: 55-5803-64-15            mail: tere.omega1@hotmail.com</p></td>
                        
                    </tr>


                <br>
                   
                </div>
               
            </main>
        </div>
    </body>
</html>
