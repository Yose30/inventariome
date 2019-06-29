<template>
    <div>
        <!-- <h4>Generar reporte</h4> -->
        <b-tabs content-class="mt-3">
            <b-tab title="Remisiones" active>
                <div class="row">
                    <div class="col-md-3">
                        <b-row class="my-1">
                            <b-col sm="5">
                                <label for="input-numero">Remision</label>
                            </b-col>
                            <b-col sm="7">
                                <b-form-input 
                                    id="input-numero" 
                                    type="number" 
                                    v-model="num_remision" 
                                    @keyup.enter="porNumero">
                                </b-form-input>
                                <div class="text-danger">{{ respuesta_numero }}</div>
                            </b-col>
                        </b-row>
                    </div>
                    <div class="col-md-5">
                        <b-row class="my-1">
                            <b-col sm="3">
                                <label for="input-cliente">Cliente</label>
                            </b-col>
                            <b-col sm="9">
                                <b-input
                                v-model="queryCliente"
                                @keyup="mostrarClientes"
                                ></b-input>
                                <div class="list-group" v-if="resultsClientes.length">
                                    <a 
                                        href="#" 
                                        v-bind:key="i" 
                                        class="list-group-item list-group-item-action" 
                                        v-for="(result, i) in resultsClientes" 
                                        @click="porCliente(result)">
                                        {{ result.name }}
                                    </a>
                                </div>
                            </b-col>
                        </b-row>
                        <hr>
                        <b-row class="my-1">
                            <b-col sm="3">
                                <label for="input-inicio">Inicio</label>
                            </b-col>
                            <b-col sm="9">
                                <input 
                                    class="form-control" 
                                    type="date" 
                                    v-model="inicio"
                                    @change="porFecha">
                                </input>
                                <div class="text-danger">{{ respuesta_fecha }}</div>
                            </b-col>
                        </b-row>
                        <b-row class="my-1">
                            <b-col sm="3">
                                <label for="input-final">Final</label>
                            </b-col>
                            <b-col sm="9">
                                <input 
                                    class="form-control" 
                                    type="date" 
                                    v-model="final"
                                    @change="porFecha">
                                </input>
                            </b-col>
                        </b-row>
                    </div>
                    <div class="col-md-4">
                        <b-form-select v-model="selected" :options="options" @change="porEstado"></b-form-select>
                    </div>
                </div>
                <hr>
                <div align="right">
                    <a 
                        class="btn btn-info col-md-1"
                        v-if="imprimirCliente && remisiones.length"
                        :href="'/imprimirCliente/' + cliente_id + '/' + inicio + '/' + final">
                        <i class="fa fa-print"></i>
                    </a>
                    <a 
                        class="btn btn-info col-md-1"
                        v-if="imprimirEstado && remisiones.length"
                        :href="'/imprimirEstado/' + estadoRemision">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
                <hr>
                <div>
                    <table class="table" v-if="tabla_numero">
                        <thead>
                            <tr>
                                <th scope="col">Folio</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Total Salida</th>
                                <th scope="col">Total Devolución</th>
                                <th scope="col">Total a pagar</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Fecha de entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ remision.id }}</td>
                                <td>{{ cliente_nombre }}</td>
                                <td>$ {{ remision.total }}</td>
                                <td>$ {{ remision.total_devolucion }}</td>
                                <td>$ {{ remision.total_pagar }}</td>
                                <td>
                                    <b-badge variant="secondary" v-if="remision.estado == 'Iniciado'">{{ remision.estado }}</b-badge>
                                    <b-badge variant="primary" v-if="remision.estado == 'Proceso'">{{ remision.estado }}</b-badge>
                                    <b-badge variant="success" v-if="remision.estado == 'Terminado'">{{ remision.estado }}</b-badge>
                                </td>
                                <td>{{ remision.fecha_entrega }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table" v-if="tabla_gral && remisiones.length">
                        <thead>
                            <tr>
                                <th scope="col">Folio</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Total Salida</th>
                                <th scope="col">Total Devolución</th>
                                <th scope="col">Total a pagar</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Fecha de entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(remision, i) in remisiones" v-bind:key="i">
                                <td>{{ remision.id }}</td>
                                <td>{{ remision.cliente.name }}</td>
                                <td>$ {{ remision.total }}</td>
                                <td>$ {{ remision.total_devolucion }}</td>
                                <td>$ {{ remision.total_pagar }}</td>
                                <td>
                                    <b-badge variant="secondary" v-if="remision.estado == 'Iniciado'">{{ remision.estado }}</b-badge>
                                    <b-badge variant="primary" v-if="remision.estado == 'Proceso'">{{ remision.estado }}</b-badge>
                                    <b-badge variant="success" v-if="remision.estado == 'Terminado'">{{ remision.estado }}</b-badge>
                                </td>
                                <td>{{ remision.fecha_entrega }}</td>
                            </tr>
                            <tr>
                                <td></td><td></td>
                                <td><b>$ {{ total_salida }}</b></td>
                                <td><b>$ {{ total_devolucion }}</b></td>
                                <td><b>$ {{ total_pagar }}</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </b-tab>
            <b-tab title="Libros">
                <div class="row">
                    <div class="col-md-4">
                        <b-row class="my-1">
                            <b-col sm="3">
                                <label for="input-cliente">Libro</label>
                            </b-col>
                            <b-col sm="9">
                                <b-input
                                    v-model="queryTitulo"
                                    @keyup="mostrarLibros"
                                ></b-input>
                                <div class="list-group" v-if="resultslibros.length">
                                    <a 
                                        href="#" 
                                        v-bind:key="i" 
                                        class="list-group-item list-group-item-action" 
                                        v-for="(libro, i) in resultslibros" 
                                        @click="datosLibro(libro)">
                                        {{ libro.titulo }}
                                    </a>
                                </div>
                            </b-col>
                        </b-row>
                    </div>
                    <div class="col-md-4">
                        <b-form-select 
                            v-model="selected2" 
                            :options="options" 
                            @change="porEstadoLibros" 
                            :disabled="disabled">
                        </b-form-select>
                    </div>
                    <div class="col-md-4">
                    
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ISBN</th>
                            <th scope="col">Libro</th>
                            <th scope="col">Unidades vendidas</th>
                            <th scope="col">Total</th>
                            <th scope="col">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                            <td>{{ devolucion.libro.ISBN }}</td>
                            <td>{{ devolucion.libro.titulo }}</td>
                            <td>{{ devolucion.unidades_resta}}</td>
                            <td>$ {{ devolucion.total_resta}}</td>
                            <td>
                                <b-badge variant="secondary" v-if="devolucion.remision.estado == 'Iniciado'">{{ devolucion.remision.estado }}</b-badge>
                                <b-badge variant="primary" v-if="devolucion.remision.estado == 'Proceso'">{{ devolucion.remision.estado }}</b-badge>
                                <b-badge variant="success" v-if="devolucion.remision.estado == 'Terminado'">{{ devolucion.remision.estado }}</b-badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </b-tab>
        </b-tabs>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                num_remision: 0,
                inicio: '',
                final: '',
                respuesta_numero: '',
                respuesta_fecha: '',
                remisiones: [],
                remision: {},
                cliente_nombre: '',
                tabla_numero: false,
                queryCliente: '',
                resultsClientes: [],
                total_salida: 0,
                total_devolucion: 0,
                total_pagar: 0,
                tabla_gral: true,
                currentTime: null,
                cliente_id: 0,
                selected: null,
                selected2: null,
                options: [
                    { value: null, text: 'Selecciona una opcion' },
                    { value: 'Iniciado', text: 'Iniciado' },
                    { value: 'Proceso', text: 'Proceso' },
                    { value: 'Terminado', text: 'Terminado'}
                ],
                imprimirCliente: false,
                imprimirEstado:false,
                estadoRemision: '',
                queryTitulo: '',
                resultslibros: [],
                tabla_libros: false,
                devoluciones: [],
                disabled: true,
            }
        },
        methods: {
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        this.remision = response.data.remision;
                        this.cliente_nombre = response.data.cliente_nombre;
                        this.tabla_gral = false;
                        this.tabla_numero = true;
                        this.imprimirCliente = false;
                        this.imprimirEstado = false;
                    }).catch(error => {
                        console.log(error.response);
                        this.respuesta_numero = 'No existe';
                    });
                }
            },
            //Se repite en remision
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{
                    axios.get('/todos_los_clientes').then(response => {
                        this.imprimirCliente = true;
                        this.imprimirEstado = false;
                        this.cliente_id = 0;
                        if(this.inicio == '' || this.final == ''){
                            this.inicio = '0000-00-00';
                            this.final = '0000-00-00';
                        }
                        this.valores(response);
                    });
                }
            },
            porCliente(result){
                axios.get('/buscar_por_cliente', {params: {id: result.id, inicio: this.inicio, final: this.final}}).then(response => {
                    this.cliente_id = result.id;
                    this.queryCliente = '';
                    this.resultsClientes = [];

                    if(this.inicio == '' || this.final == ''){
                        this.inicio = '0000-00-00';
                        this.final = '0000-00-00';
                    }
                    this.imprimirEstado = false;
                    this.imprimirCliente = true;

                    this.valores(response);
                });
            },
            porFecha(){
                axios.get('/buscar_por_fecha', {params: {inicio: this.inicio, final: this.final, cliente_id: this.cliente_id}}).then(response => {
                    this.valores(response);
                    if(this.final != ''){
                        this.imprimirEstado = false;
                        this.imprimirCliente = true;
                    }
                });
            },
            acumular(){
                this.total_salida = 0;
                this.total_devolucion = 0;
                this.total_pagar = 0;
                this.remisiones.forEach(remision => {
                    this.total_salida += remision.total;
                    this.total_devolucion += remision.total_devolucion;
                    this.total_pagar += remision.total_pagar;
                });
            },
            valores(response){
                this.remisiones = [];
                this.remisiones = response.data;
                this.tabla_numero = false;
                this.tabla_gral = true;
                this.acumular();
            },
            porEstado(){
                axios.get('/buscar_por_estado', {params: {estado: this.selected}}).then(response => {
                    this.valores(response);
                    this.imprimirCliente = false;
                    this.imprimirEstado = true;
                    this.estadoRemision = this.selected;
                });
            },
            //Mostrar resultados de la busqueda por titulo del libro
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.resultslibros = response.data;
                    });
                }
                else{
                    axios.get('/todos_los_libros').then(response => {
                        this.devoluciones = response.data;
                        this.disabled = false;
                    });
                } 
            },
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                console.log(libro);
            },
            //Mostrar resultados de libros con estado
            porEstadoLibros(){
                axios.get('/buscar_por_estado_libros', {params: {estado: this.selected2}}).then(response => {
                    console.log(response.data);
                });
            },
        }
    }
</script>