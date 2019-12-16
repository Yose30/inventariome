<template>
    <div>
        <check-connection-component></check-connection-component>
       |<div v-if="listadoLibros"> 
            <b-row>
                <!-- MOSTRAR LIBROS POR CLIENTE -->
                <b-col sm="4">
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="10">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes()"></b-input>
                            <div class="list-group" v-if="resultsClientes.length" id="listaL">
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
                    <!-- <hr>
                    <b-button variant="info" pill v-b-modal.modal-ayudaV><i class="fa fa-info-circle"></i> Ayuda</b-button> -->
                </b-col>
                <!-- MOSTRAR LIBROS POR EDITORIALES -->
                <b-col sm="4">
                    <!-- BUSCAR LIBRO POR TITULO -->
                    <b-row class="my-1">
                        <b-col sm="2">
                            <label for="input-cliente">Libro</label>
                        </b-col>
                        <b-col sm="10">
                            <b-input
                                v-model="queryTitulo"
                                @keyup="mostrarLibros()"
                            ></b-input>
                            <div class="list-group" v-if="resultslibros.length" id="listaL">
                                <a 
                                    class="list-group-item list-group-item-action" 
                                    href="#" 
                                    v-bind:key="i" 
                                    v-for="(libro, i) in resultslibros" 
                                    @click="buscarLibro(libro)">
                                    {{ libro.titulo }}
                                </a>
                            </div>
                        </b-col>
                    </b-row>
                    <hr>
                    <b-row>
                        <b-col sm="3">
                            <label for="input-editorial">Editorial</label>
                        </b-col>
                        <b-col sm="9">
                            <b-form-select v-model="editorial" :options="options" @change="mostrarEditoriales()"></b-form-select>
                        </b-col> 
                    </b-row>
                </b-col>
                <!-- MOSTRAR LIBROS POR FECHA -->
                <b-col sm="4">
                    <b-row class="my-1">
                        <b-col sm="2"><label for="input-inicio">De:</label></b-col>
                        <b-col sm="10"><b-input type="date" v-model="fecha1" :state="state" @change="porFecha()"/></b-col>
                    </b-row>
                    <b-row class="my-1">
                        <b-col sm="2"> <label for="input-final">A: </label></b-col>
                        <b-col sm="10"><b-input type="date" v-model="fecha2" @change="porFecha()"/></b-col>
                    </b-row>
                </b-col>
            </b-row><br>
            <!-- DATOS DE BUSQUEDA POR LIBRO -->
            <div v-if="busquedaLibro">
                <b-row>
                    <b-col>
                        <!-- PAGINACIÓN -->
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="clientes.length"
                            :per-page="perPage"
                            aria-controls="my-table"
                            v-if="clientes.length > 0">
                        </b-pagination>
                    </b-col>
                    <b-col class="text-right">
                        <!-- DESCARGAR REPORTE POR LIBRO -->
                        <b-button v-if="busquedaLibro && clientes.length > 0" variant="dark" :href="'/downLibroEX/' + libro_id + '/' + fecha1 + '/' + fecha2">
                            <i class="fa fa-download"></i> Descargar
                        </b-button>
                    </b-col>
                </b-row>
                <!-- LISTADO DE CLIENTES -->
                <b-table v-if="clientes.length > 0" :items="clientes" :fields="fieldsLibro">
                    <template slot="unidades_vendido" slot-scope="row">
                        {{ row.item.unidades_vendido | formatNumber }}
                    </template>
                    <template slot="total_vendido" slot-scope="row">
                        ${{ row.item.total_vendido | formatNumber }}
                    </template>
                    <template slot="unidades_pendiente" slot-scope="row">
                        {{ row.item.unidades_pendiente | formatNumber }}
                    </template>
                    <template slot="total_pendiente" slot-scope="row">
                        ${{ row.item.total_pendiente | formatNumber }}
                    </template>
                    <!-- ENCABEZADO DE TOTALES -->
                    <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th></th>
                            <th>{{ unidades_vendido | formatNumber }}</th>
                            <th>${{ subtotal_vendidas | formatNumber }}</th>
                            <th>{{ unidades_pendiente | formatNumber }}</th>
                            <th>${{ subtotal_pendientes | formatNumber }}</th>
                        </tr>
                    </template>
                </b-table>
                <b-alert v-else show variant="secondary"><i class="fa fa-warning"></i> No se encontrarón registros</b-alert>
            </div>
            <!-- DATOS DE BSUQEDA POR CLIENTE Y EDITORIAL -->
            <div v-else>
                <b-row>
                    <b-col>
                        <!-- PAGINACIÓN -->
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="vendidos.length"
                            :per-page="perPage"
                            aria-controls="my-table"
                            v-if="vendidos.length > 0">
                        </b-pagination>
                    </b-col>
                    <b-col class="text-right">
                        <!-- DESCARGAR REPORTE POR CLIENTE -->
                        <b-button v-if="busquedaCliente && vendidos.length > 0" variant="dark" :href="'/downClienteEX/' + cliente_id + '/' + fecha1 + '/' + fecha2">
                            <i class="fa fa-download"></i> Descargar
                        </b-button>
                        <!-- DESCARGAR REPORTE POR EDITORIAL -->
                        <b-button v-if="busquedaEditorial && vendidos.length > 0" variant="dark" :href="'/downEditorialEX/' + editorial + '/' + fecha1 + '/' + fecha2">
                            <i class="fa fa-download"></i> Descargar
                        </b-button>
                        <b-button v-if="busquedaEditorial && editorial === 'TODO'" variant="dark" :href="'/downDetalladoEX/' + fecha1 + '/' + fecha2">
                            <i class="fa fa-download"></i> Detallado
                        </b-button>
                    </b-col>
                </b-row>
                <!-- LISTADO DE LIBROS -->
                <b-table 
                    responsive
                    :items="vendidos" 
                    :fields="fields" 
                    v-if="vendidos.length > 0" 
                    :per-page="perPage"
                    :current-page="currentPage"
                    id="my-table">
                    <template slot="unidades_vendido" slot-scope="row">
                        {{ row.item.unidades_vendido | formatNumber }}
                    </template>
                    <template slot="total_vendido" slot-scope="row">
                        ${{ row.item.total_vendido | formatNumber }}
                    </template>
                    <template slot="unidades_pendiente" slot-scope="row">
                        {{ row.item.unidades_pendiente | formatNumber }}
                    </template>
                    <template slot="total_pendiente" slot-scope="row">
                        ${{ row.item.total_pendiente | formatNumber }}
                    </template>
                    <template slot="detalles" slot-scope="row">
                        <b-button v-if="!busquedaCliente" variant="info" @click="func_detalles(row.item)">Detalles</b-button>
                    </template>
                    <!-- ENCABEZADO DE TOTALES -->
                    <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th></th>
                            <th>{{ unidades_vendido | formatNumber }}</th>
                            <th>${{ subtotal_vendidas | formatNumber }}</th>
                            <th>{{ unidades_pendiente | formatNumber }}</th>
                            <th>${{ subtotal_pendientes | formatNumber }}</th>
                        </tr>
                    </template>
                </b-table>
                <b-alert v-else show variant="secondary"><i class="fa fa-warning"></i> No se encontrarón registros</b-alert>
            </div>
        </div>
        <!-- DETALLES -->
        <div v-else>
            <b-row>
                <b-col sm="10"><b>{{ libro }}</b></b-col>
                <b-col sm="2">
                    <b-button variant="secondary" @click="listadoLibros = true;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <hr>
            <b-table :items="registros" :fields="fieldsLibro">
                <template slot="unidades_vendido" slot-scope="row">
                        {{ row.item.unidades_vendido | formatNumber }}
                    </template>
                    <template slot="total_vendido" slot-scope="row">
                        ${{ row.item.total_vendido | formatNumber }}
                    </template>
                    <template slot="unidades_pendiente" slot-scope="row">
                        {{ row.item.unidades_pendiente | formatNumber }}
                    </template>
                    <template slot="total_pendiente" slot-scope="row">
                        ${{ row.item.total_pendiente | formatNumber }}
                    </template>
            </b-table>
        </div>
        <!-- MODALS -->
        <b-modal id="modal-ayudaV" title="Ayuda">
            <h5><b>Búsqueda</b></h5>
            <p>
                <ul>
                    <li><b>Búsqueda por editorial</b>: Elegir la editorial entre las opciones que aparecen. Se mostrarán todos los libros vendidos de la editorial seleccionada.</li>
                    <li><b>Búsqueda por cliente</b>: Escribir el nombre del cliente y elegir entre las opciones que aparezcan.</li>
                    <li><b>Búsqueda por fecha</b>: Elegir fecha de inicio y fecha final para mostrar los libros vendidos en ese rango de fechas.</li>
                    <li><b>Búsqueda por editorial y fecha</b>: Elegir la editorial entre las opciones que aparecen y después seleccionar el rango de fechas.</li>
                    <li><b>Búsqueda por cliente y fecha</b>: Escribir el nombre del cliente para elegir entre las opciones que aparezcan y despues elegir el rango de fechas.</li>
                </ul>
            </p>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: ['registersall', 'editoriales'],
        data() {
            return {
                fecha1: '0000-00-00',
                fecha2: '0000-00-00',
                vendidos: this.registersall,
                fields: [
                    'libro', 
                    {key: 'unidades_vendido', label: 'Unidades vendidas'},
                    {key: 'total_vendido', label: 'Subtotal'},
                    {key: 'unidades_pendiente', label: 'Unidades pendientes'},
                    {key: 'total_pendiente', label: 'Subtotal'},
                    {key: 'detalles', label: ''}
                ],
                fieldsLibro: [
                    'cliente',
                    {key: 'unidades_vendido', label: 'Unidades vendidas'},
                    {key: 'total_vendido', label: 'Subtotal'},
                    {key: 'unidades_pendiente', label: 'Unidades pendientes'},
                    {key: 'total_pendiente', label: 'Subtotal'},
                    {key: 'detalles', label: ''}
                ],
                listadoLibros: true,
                registros: [],
                editorial: 'TODO',
                options: [],
                perPage: 10,
                currentPage: 1,
                libro: '',
                state: null,
                queryCliente: '',
                resultsClientes: [],
                cliente_id: null,
                busquedaCliente: false,
                busquedaLibro: false,
                busquedaEditorial: true,
                libro_id: null,
                queryTitulo: '',
                resultslibros: [],
                clientes: [],
                unidades_vendido: 0,
                subtotal_vendidas: 0,
                unidades_pendiente: 0,
                subtotal_pendientes: 0
            }
        },
        filters: {
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        created: function() {
            this.acumular_totales(this.vendidos);
            this.assign_editorial();
        },
        methods: {
            assign_editorial(){
                this.options.push({
                    value: 'TODO',
                    text: 'MOSTRAR TODO'
                });
                this.editoriales.forEach(editorial => {
                    this.options.push({
                        value: editorial.editorial,
                        text: editorial.editorial
                    });
                });
            },
            // MOSTRAR COINCIDENCIA DE CLIENTES
            mostrarClientes() {
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{ this.resultsClientes = []; }
            },
            // ASIGNAR DATOS DEL CLIENTE SELECCIONADO
            porCliente(result) {
                axios.get('/obtener_cliente', {params: {cliente_id: result.id}}).then(response => {
                    this.resultsClientes = [];
                    this.queryCliente = result.name;
                    this.cliente_id = result.id;
                    this.vendidos = response.data;
                    this.acumular_totales(this.vendidos);
                    this.ini_busquedad(true, false, false);
                    this.queryTitulo = '';
                    this.editorial = 'TODO';
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // MOSTRAR COINCIDENCIA DE LIBROS
            mostrarLibros(){
                if(this.queryTitulo.length > 0){
                   axios.get('/mostrarLibros', {params: {queryTitulo: this.queryTitulo}}).then(response => {
                        this.resultslibros = response.data;
                    });
                } 
                else{ this.resultslibros = []; }
            },
            // MOSTRAR LAS VENTAS POR LIBRO
            buscarLibro(libro) {
                axios.get('/obtener_libro', {params: {libro_id: libro.id}}).then(response => {
                    this.resultslibros = [];
                    this.queryTitulo = libro.titulo;
                    this.libro_id = libro.id;
                    this.clientes = response.data;
                    this.acumular_totales(this.clientes);
                    this.ini_busquedad(false, true, false);
                    this.queryCliente = '';
                    this.editorial = 'TODO';
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // MOSTRAR LIBROS POR EDITORIAL
            mostrarEditoriales(){
                this.ini_busquedad(false, false, true);
                if(this.editorial !== 'TODO'){
                    axios.get('/obtener_editorial', {params: {editorial: this.editorial}}).then(response => {
                        this.vendidos = response.data;
                        this.acumular_totales(this.vendidos);
                        this.queryTitulo = '';
                        this.queryCliente = '';
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                    });
                }
                else{
                    this.getTodo();
                    this.acumular_totales(this.vendidos);
                } 
            },
            // OBTENER TODOS LOS LIBROS VENDIDOS
            getTodo(){
                axios.get('/obtener_vendidos').then(response => {
                    this.vendidos = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // OBTENER POR FECHA
            porFecha(){
                if(this.fecha2 != '0000-00-00'){
                    if(this.fecha1 != '0000-00-00') {
                        this.state = null;
                        if(this.busquedaCliente) {
                            // BUSQUEDA DE CLIENTE POR FECHA
                            axios.get('/cliente_por_fecha', {params: {cliente_id: this.cliente_id, fecha1: this.fecha1, fecha2: this.fecha2}}).then(response => {
                                this.vendidos = response.data;
                                this.acumular_totales(this.vendidos);
                            }).catch(error => {
                                this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                            });
                        }
                        if(this.busquedaLibro) {
                            // BUSQUEDA DE CLIENTE POR FECHA
                            axios.get('/libro_por_fecha', {params: {libro_id: this.libro_id, fecha1: this.fecha1, fecha2: this.fecha2}}).then(response => {
                                this.clientes = response.data;
                                this.acumular_totales(this.clientes);
                            }).catch(error => {
                                this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                            });
                        }
                        if(this.busquedaEditorial){
                            if(this.editorial === 'TODO'){
                                // BUSQUEDA DE EDITORIAL POR FECHA
                                axios.get('/obtener_por_fecha', {params: {fecha1: this.fecha1, fecha2: this.fecha2}}).then(response => {
                                    this.vendidos = response.data;
                                    this.acumular_totales(this.vendidos);
                                }).catch(error => {
                                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                                });
                            } else {
                                // BUSQUEDA DE EDITORIAL POR FECHA
                                axios.get('/editorial_por_fecha', {params: {editorial: this.editorial, fecha1: this.fecha1, fecha2: this.fecha2}}).then(response => {
                                    this.vendidos = response.data;
                                    this.acumular_totales(this.vendidos);
                                }).catch(error => {
                                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                                });
                            }
                        }
                    } else {
                        this.state = false;
                        this.makeToast('warning', 'Es necesario seleccionar la fecha de inicio');
                    }
                }
            },
            // OBTENER DETALLES DEL LIBRO VENDIDO
            func_detalles(vendido){
                this.listadoLibros = false;
                axios.get('/detalles_vendidos', {params: {fecha1: this.fecha1, fecha2: this.fecha2, libro_id: vendido.libro_id}}).then(response => {
                    this.libro = vendido.libro;
                    this.registros = response.data;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // OCULTAR Y/O MOSTRAR LA BUSQUEDA
            ini_busquedad(cliente, libro, editorial){
                this.busquedaCliente = cliente;
                this.busquedaLibro = libro;
                this.busquedaEditorial = editorial;
                if(this.busquedaLibro) { this.vendidos = []; }
                else{ this.clientes = []; }
                this.fecha1 = '0000-00-00';
                this.fecha2 = '0000-00-00';
            },
            acumular_totales(registros){
                this.ini_totales();
                registros.forEach(registro => {
                    this.unidades_vendido += parseInt(registro.unidades_vendido);
                    this.subtotal_vendidas += parseInt(registro.total_vendido);
                    this.unidades_pendiente += parseInt(registro.unidades_pendiente);
                    this.subtotal_pendientes += parseInt(registro.total_pendiente);
                });
            },
            ini_totales(){
                this.unidades_vendido = 0;
                this.subtotal_vendidas = 0;
                this.unidades_pendiente = 0;
                this.subtotal_pendientes = 0;
            },
            makeToast(variante = null, descripcion){
                this.$bvToast.toast(descripcion, {
                    title: 'Mensaje',
                    variant: variante,
                    solid: true
                });
            }
        }
    }
</script>
