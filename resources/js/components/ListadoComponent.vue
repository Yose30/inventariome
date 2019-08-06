<template>
    <div>
        <div v-if="mostrar && !detalles">
            <h4>Remisiones</h4>
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
                <div class="col-md-4">
                    <!-- <div class="row">
                        <label class="col-md-3">Estado</label>
                        <div class="col-md-9">
                            <b-form-select v-model="selected" :options="options" @change="porEstado"></b-form-select>
                        </div>
                    </div>
                    <hr> -->
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
                </div>
                <div class="col-md-5">
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
            </div>
            <hr>
            <div align="right">
                <a 
                    class="btn btn-info"
                    v-if="imprimirCliente && remisiones.length"
                    :href="'/imprimirCliente/' + cliente_id + '/' + inicio + '/' + final">
                    <i class="fa fa-download"></i> Descargar
                </a>
                <a 
                    class="btn btn-info"
                    v-if="imprimirEstado && remisiones.length"
                    :href="'/imprimirEstado/' + estadoRemision">
                    <i class="fa fa-download"></i> Descargar
                </a>
            </div>
            <hr>
            <div>
                <table class="table" v-if="tabla_numero">
                    <thead>
                        <tr>
                            <th scope="col">Folio</th>
                            <th scope="col">Fecha de creación</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Salida</th>
                            <th scope="col">Devolución</th>
                            <th scope="col">Pagos</th>
                            <th scope="col">Pagar</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ remision.id }}</td>
                            <td>{{ remision.fecha_creacion }}</td>
                            <td>{{ cliente_nombre }}</td>
                            <td>$ {{ remision.total }}</td>
                            <td>$ {{ remision.total_devolucion }}</td>
                            <td>$ {{ remision.pagos }}</td>
                            <td>$ {{ remision.total_pagar }}</td>
                            <td>
                                <button 
                                    class="btn btn-primary" 
                                    @click="detallesRemision(remision)">
                                    Detalles
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" v-if="tabla_gral && remisiones.length">
                    <thead>
                        <tr>
                            <th scope="col">Folio</th>
                            <th scope="col">Fecha de creación</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Salida</th>
                            <th scope="col">Devolución</th>
                            <th scope="col">Pagos</th>
                            <th scope="col">Pagar</th>
                            <!-- <th scope="col">Estado</th> -->
                            <!-- <th scope="col">Fecha de entrega</th> -->
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="(remision, i) in remisiones" 
                            v-bind:key="i" 
                            :class="`${remision.estado == 'Cancelado' ? 'table-danger' : 'null'}`"> 
                            <td>{{ remision.id }}</td>
                            <td>{{ remision.fecha_creacion }}</td>
                            <td>{{ remision.cliente.name }}</td>
                            <td>$ {{ remision.total }}</td>
                            <td>
                                <label v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">$ {{ remision.total_devolucion }}</label>
                            </td>
                            <td>
                                <label v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">$ {{ remision.pagos }}</label>

                            </td>
                            <td>
                                <b-badge v-if="remision.total_pagar == 0 && remision.pagos > 0" variant="success">Pagado</b-badge>
                                <label v-if="remision.total_pagar > 0">
                                    $ {{ remision.total_pagar }}
                                </label>
                            </td>
                            <td>
                                <button 
                                    class="btn btn-primary" 
                                    @click="detallesRemision(remision)">
                                    Detalles
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td></td><td></td><td></td>
                            <td><b>$ {{ total_salida }}</b></td>
                            <td><b>$ {{ total_devolucion }}</b></td>
                            <td><b>$ {{ total_pagos }}</b></td>
                            <td><b>$ {{ total_pagar }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-if="detalles">
            <b-row>
                <b-col sm="5">
                    <h4>Remisión N. {{ remision.id }}</h4>
                    <label>Cliente: {{ remision.cliente.name }}</label>    
                </b-col>
                <b-col sm="3">
                    <b-button 
                        variant="outline-danger" 
                        v-b-modal.modal-cancelar 
                        v-if="remision.estado == 'Iniciado' && role_id == 2"
                    >
                        <i class="fa fa-close"></i> Cancelar remisión
                    </b-button>
                </b-col>
                <b-col sm="2" align="left">
                    <b-badge variant="info" v-if="remision.estado == 'Iniciado'">{{ remision.estado }}</b-badge>
                    <b-badge variant="primary" v-if="remision.estado == 'Proceso'">Entregado</b-badge>
                    <b-badge variant="danger" v-if="remision.estado == 'Cancelado'">Remisión cancelada</b-badge>
                </b-col>
                <b-col sm="2" align="right">
                    <b-button 
                        variant="secondary"
                        @click="detalles = false">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <div class="row">
                <h4 class="col-md-10">Salida</h4>
                <b-button 
                    variant="link" 
                    :class="mostrarSalida ? 'collapsed' : null"
                    :aria-expanded="mostrarSalida ? 'true' : 'false'"
                    aria-controls="collapse-1"
                    @click="mostrarSalida = !mostrarSalida">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-1" v-model="mostrarSalida" class="mt-2">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ISBN</th>
                            <th scope="col">Libro</th>
                            <th scope="col">Costo unitario</th>
                            <th scope="col">Unidades</th>
                            <th scope="col">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(registro, i) in registros" v-bind:key="i">
                            <td>{{ registro.libro.ISBN }}</td>
                            <td>{{ registro.libro.titulo }}</td>
                            <td>$ {{ registro.costo_unitario }}</td>
                            <td>{{ registro.unidades }}</td>
                            <td>$ {{ registro.total }}</td>
                        </tr>
                        <tr>
                            <td></td><td></td>
                            <td></td><td></td>
                            <td><h5>$ {{ remision.total }}</h5></td>
                        </tr>
                    </tbody>
                </table>
            </b-collapse>
            <hr>
            <div class="row" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                <h4 class="col-md-10">Devolución</h4>
                <b-button 
                    variant="link" 
                    :class="mostrarDevolucion ? 'collapsed' : null"
                    :aria-expanded="mostrarDevolucion ? 'true' : 'false'"
                    aria-controls="collapse-2"
                    @click="mostrarDevolucion = !mostrarDevolucion">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-2" v-model="mostrarDevolucion" class="mt-2">
                <table class="table" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                    <thead>
                        <tr>
                            <th scope="col">ISBN</th>
                            <th scope="col">Libro</th>
                            <th scope="col">Costo unitario</th>
                            <th scope="col">Unidades</th>
                            <th scope="col">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                            <td>{{ devolucion.libro.ISBN }}</td>
                            <td>{{ devolucion.libro.titulo }}</td>
                            <td>$ {{ devolucion.dato.costo_unitario }}</td>
                            <td>{{ devolucion.unidades }}</td>
                            <td>$ {{ devolucion.total }}</td>
                        </tr>
                        <tr>
                            <td></td><td></td>
                            <td></td><td></td>
                            <td><h5>$ {{ remision.total_devolucion }}</h5></td>
                        </tr>
                    </tbody>
                </table>
            </b-collapse>
            <hr>
            <div class="row" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                <h4 class="col-md-10">Pagos</h4>
                <b-button 
                    variant="link" 
                    :class="mostrarPagos ? 'collapsed' : null"
                    :aria-expanded="mostrarPagos ? 'true' : 'false'"
                    aria-controls="collapse-3"
                    @click="mostrarPagos = !mostrarPagos">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-3" v-model="mostrarPagos" class="mt-2">
                <b-table :items="vendidos" :fields="fieldsP">
                    <template slot="isbn" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="libro" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario }}</template>
                    <template slot="subtotal" slot-scope="row">${{ row.item.total }}</template>
                    <template slot="detalles" slot-scope="row">
                        <b-button v-if="row.item.pagos.length > 0" variant="outline-info" @click="row.toggleDetails">
                            {{ row.detailsShowing ? 'Ocultar' : 'Mostrar'}} detalles
                        </b-button>
                    </template>
                    <template slot="row-details" slot-scope="row">
                        <b-card>
                            <b-table :items="row.item.pagos" :fields="fieldsD">
                                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                                <template slot="user_id" slot-scope="row">
                                    <label v-if="row.item.user_id == 2">Teresa Pérez</label>
                                    <label v-if="row.item.user_id == 3">Almacén</label>
                                </template>
                                <template slot="unidades" slot-scope="row">{{ row.item.unidades }}</template>
                                <template slot="pago" slot-scope="row">$ {{ row.item.pago }}</template>
                                <template slot="created_at" slot-scope="row">{{ row.created_at | moment }}</template>
                            </b-table>
                        </b-card>
                    </template>
                </b-table>
            </b-collapse>
            <hr>
            <div class="row" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                <h4 class="col-md-10">Pagar</h4>
                <b-button 
                    variant="link" 
                    :class="mostrarFinal ? 'collapsed' : null"
                    :aria-expanded="mostrarFinal ? 'true' : 'false'"
                    aria-controls="collapse-3"
                    @click="mostrarFinal = !mostrarFinal">
                    <i class="fa fa-sort-asc"></i>
                </b-button>
            </div>
            <b-collapse id="collapse-3" v-model="mostrarFinal" class="mt-2">
                <table class="table" v-if="remision.estado == 'Proceso' || remision.estado == 'Terminado'">
                    <thead>
                        <tr>
                            <th scope="col">ISBN</th>
                            <th scope="col">Libro</th>
                            <th scope="col">Costo unitario</th>
                            <th scope="col">Unidades</th>
                            <th scope="col">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(devolucion, i) in devoluciones" v-bind:key="i">
                            <td>{{ devolucion.libro.ISBN }}</td>
                            <td>{{ devolucion.libro.titulo }}</td>
                            <td>$ {{ devolucion.dato.costo_unitario }}</td>
                            <td>{{ devolucion.unidades_resta }}</td>
                            <td>$ {{ devolucion.total_resta }}</td>
                        </tr>
                        <tr>
                            <td></td><td></td><td></td><td></td>
                            <td><h5>$ {{ remision.total_pagar }}</h5></td>
                        </tr>
                    </tbody>
                </table>
            </b-collapse>
        </div>
        <b-modal id="modal-cancelar" title="Cancelar remisión">
            <p><b><i class="fa fa-exclamation-triangle"></i> ¿Estas seguro de cancelar la remisión?</b></p>
            <div slot="modal-footer">
                <b-button @click="cambiarEstado">OK</b-button>
            </div>
        </b-modal>
    </div>
</template>

<script>
    moment.locale('es');
    export default {
        props: ['role_id'],
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
                selected2: 'Terminado',
                options: [
                    { value: 'Terminado', text: 'Terminado'},
                    { value: 'Proceso', text: 'Proceso' },
                    { value: 'Iniciado', text: 'Iniciado' },
                ],
                imprimirCliente: false,
                imprimirEstado:false,
                estadoRemision: '',
                queryTitulo: '',
                resultslibros: [],
                tabla_libros: false,
                libros: [],
                //ATRIBUTOS EDITAR REMISION
                mostrar: true,
                show: false,
                mostrarActualizar: true,
                btnInformacion: true,
                mostrarDatos: false,
                dato: {},
                formularioEditar: false,
                errors: {},
                fecha: '',
                inputFecha: true,
                bdremision: {
                    id: 0,
                    cliente_id: 0,
                    total: 0,
                    fecha_entrega: ''
                },
                items: [],
                botonEliminar: false,
                total_remision: 0,
                descuento: 0,
                pagar: 0,
                isbn: '',
                inputISBN: false,
                respuestaISBN: '',
                temporal: {},
                buscarTitulo: '',
                inputLibro: false,
                resultadoslibros: [],
                costo_unitario: 0,
                inputCosto: false,
                respuestaCosto: '',
                unidades: 0,
                inputUnidades: false,
                respuestaUnidades: '',
                detalles: false,
                mostrarSalida: false,
                mostrarDevolucion: false,
                mostrarPagos: false,
                mostrarFinal: false,
                registros: [],
                devoluciones: [],
                d_total_salida: 0,
                d_total_devolucion: 0,
                d_total_pagar: 0,
                total_pagos: 0,
                vendidos: [],
                fieldsP: [
                    {key: 'isbn', label: 'ISBN'}, 
                    'libro', 
                    {key: 'costo_unitario', label: 'Costo unitario'}, 
                    // {key: 'unidades_resta', label: 'Unidades pendientes'},
                    {key: 'unidades', label: 'Unidades'}, 
                    'subtotal',
                    {key: 'detalles', label: ''}
                ],
                fieldsD: [
                    {key: 'index', label: 'N.'},
                    {key: 'user_id', label: 'Usuario'}, 
                    'unidades',
                    'pago', 
                    {key: 'created_at', label: 'Fecha'}, 
                ],
                idRemision: 0,
            }
        },
        created: function(){
			this.getTodo();
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            }
        },
        methods: {
            porNumero(){
                if(this.num_remision > 0){
                    this.respuesta_numero = '';
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        this.remision = response.data.remision;
                        this.cliente_nombre = response.data.cliente_nombre;
                        this.tabla_gral = false;
                        this.tabla_numero = true;
                        this.imprimirCliente = false;
                        this.imprimirEstado = false;
                    }).catch(error => {
                        this.makeToast('danger', 'No existe la remisión');
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
                    this.getTodo();
                }
            },
            getTodo(){ 
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
                this.total_pagos = 0;
                this.total_pagar = 0;
                this.remisiones.forEach(remision => {
                    if(remision.estado != 'Cancelado'){
                        this.total_salida += remision.total;
                        this.total_devolucion += remision.total_devolucion;
                        this.total_pagos += remision.pagos;
                        this.total_pagar += remision.total_pagar;
                    }
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
                    this.porEstadoLibros();
                } 
            },
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                this.resultslibros = [];
                this.queryTitulo = '';
                this.tabla_libros = true;
                console.log(libro);
            },
            inicializar(response){
                this.libros = [];
                this.libros = response.data;
                this.tabla_libros = true;
            },
            //Mostrar resultados de libros con estado
            porEstadoLibros(){
                axios.get('/buscar_por_estado_libros', {params: {estado: this.selected2}}).then(response => {
                    this.inicializar(response);
                });
            },

            //EDITAR REMISION
            editarRemision(remision){
                this.mostrar = false;
                this.mostrarActualizar = false;
                axios.get('/nueva_edicion', {params: {id: remision.id}}).then(response => {
                    axios.get('/getCliente', {params: {id: remision.cliente_id, remision_id: remision.id}}).then(response => {
                        this.dato = response.data.cliente;
                        this.items = response.data.datos;
                        this.items.forEach(item => {
                            this.total_remision += item.total;
                        });
                        this.bdremision = {
                            id: remision.id,
                            fecha_entrega: remision.fecha_entrega,
                            cliente_id: remision.cliente_id,
                        };
                        this.fecha = this.bdremision.fecha_entrega;
                        // this.getDescuento();
                        this.bdremision.total = this.total_remision;
                    });
                });
            },
            getDescuento(){
                this.descuento = (this.total_remision * this.dato.descuento) / 100;
                this.pagar = this.total_remision - this.descuento;
            },
            cancelarRemision(){
                this.show = false;
                this.mostrar = true;
                this.dato = {};
                this.form = {};
                this.errors = {};
                this.fecha = '';
                this.bdremision = {id: 0, cliente_id: 0, total: 0, fecha_entrega: ''};
                this.items = [];
                this.total_remision = 0;
                this.descuento = 0;
                this.pagar = 0;
                this.mostrarDatos = false;
                this.temporal = {};
                this.inputCosto = false;
                this.inputUnidades = false;
                this.inputISBN = true;
                this.inputLibro = true;
                this.buscarTitulo = '';
                this.unidades = 0;
                this.costo_unitario = 0;
            },
            actRemision(){
                this.getDescuento();
                
                this.bdremision.total = this.total_remision;

                axios.put('/actualizar_remision', this.bdremision).then(response => {
                    this.mostrarActualizar = false;
                    this.botonEliminar = false;
                    this.eliminarTemporal();
                    this.inputISBN = false;
                    this.inputLibro = false;
                    this.btnInformacion = true;
                    this.inputFecha = true;
                });
            },
            editar(){
                this.mostrarActualizar = true;
                this.botonEliminar = true;
                this.btnInformacion = false;
                this.inputFecha = false;
                this.eliminarTemporal();
            },
            editarInformacion(){
                this.formularioEditar = true;
            },
            onSubmit(){
                axios.put('/editar_cliente', this.form).then(response => {
                    this.dato = response.data;
                    this.formularioEditar = false;
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
            //Eliminar registro de la remision
            eliminarRegistro(item, i){
                axios.delete('/eliminar_registro', {params: {id: item.id}}).then(response => {
                    this.items.splice(i, 1);
                    this.total_remision = this.total_remision - item.total;
                    this.bdremision.total = this.total_remision;
                    // this.getDescuento();
                });
            },
            //Buscar libro por ISBN
            buscarLibroISBN(){
                axios.get('/buscarISBN', {params: {isbn: this.isbn}}).then(response => {
                    this.temporal = response.data;
                    this.isbn = '';
                    this.ini_1();
                }).catch(error => {
                    this.respuestaISBN = 'ISBN incorrecto';
                });
            },
            mostrarLibros(){
                if(this.buscarTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.buscarTitulo}}).then(response => {
                        this.resultadoslibros = response.data;
                    });
               } 
            },
            //Mostrar datos del libro seleccionado 
            datosLibro(libro){
                this.resultadoslibros = [];
                this.ini_1();
                this.temporal = {
                    id: libro.id,
                    ISBN: libro.ISBN,
                    titulo: libro.titulo,
                    costo_unitario: 0,
                    unidades: 0,
                    total: 0,
                    piezas: libro.piezas
                };
            },
            ini_1(){
                this.inputISBN = false;
                this.inputLibro = false;
                this.inputCosto = true;
            },
            guardarCosto(){
                this.respuestaCosto = '';
                if(this.costo_unitario > 0){
                    this.temporal.costo_unitario = this.costo_unitario;
                    this.inputCosto = false;
                    this.inputUnidades = true;
                    this.respuestaCosto = '';
                }
                else{
                    // this.respuestaCosto = 'Costo invalido';
                    this.makeToast('warning', 'Costo invalido');
                } 
            },
            eliminarTemporal(){
                this.temporal = {};
                this.inputISBN = true;
                this.respuestaISBN = '';
                this.buscarTitulo = '';
                this.inputLibro = true;
                this.unidades = 0;
                this.inputUnidades = false;
                this.respuestaUnidades = '';
                this.costo_unitario = 0;
                this.inputCosto = false;
                this.respuestaCosto = '';
            },
            cambiarEstado(){
                axios.put('/cancelar_remision', this.remision).then(response => {
                    this.remision.estado = response.data.estado;
                    this.$bvModal.hide('modal-cancelar');
                    this.makeToast('secondary', 'Remisión cancelada');
                })
                .catch(error => {
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
            },
            guardarRegistro(){
                if(this.unidades > 0){
                    this.temporal.remision_id = this.bdremision.id;
                    this.temporal.unidades = this.unidades;
                    this.temporal.total = this.unidades * this.temporal.costo_unitario;
                    
                    axios.post('/registro_remision', this.temporal).then(response => {
                        this.temporal = {
                            id: response.data.dato.id,
                            libro: {
                                ISBN: response.data.libro.ISBN,
                                titulo: response.data.libro.titulo,
                            },
                            costo_unitario: response.data.dato.costo_unitario,
                            unidades: response.data.dato.unidades,
                            total: response.data.dato.total
                        };
                        this.items.push(this.temporal);
                        this.total_remision += response.data.dato.total;
                        // this.getDescuento();
                        this.eliminarTemporal();
                    }); 
                }
            },
            detallesRemision(remision){
                this.detalles = true;
                this.remision = remision;
                this.mostrarSalida = false;
                this.mostrarDevolucion = false;
                this.mostrarPagos = false;
                this.mostrarFinal = false;
                axios.get('/lista_datos', {params: {numero: remision.id}}).then(response => {
                    
                    this.registros = response.data.datos;
                    this.devoluciones = response.data.devoluciones;
                    this.vendidos = response.data.vendidos;
                });
            },
            makeToast(variant = null, descripcion) {
                this.$bvToast.toast(descripcion, {
                    title: 'Mensaje',
                    variant: variant,
                    solid: true
                })
            }
        }
    }
</script>

<style>
    #btnCancelar {
        color: red;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
</style>