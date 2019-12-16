<template>
    <div>
        <check-connection-component></check-connection-component>
        <div v-if="listadoAdeudos">
            <b-row>
                <!-- BUSCAR ADEUDO POR NUMERO DE REMISION -->
                <b-col sm="3">
                    <b-row class="my-1">
                        <b-col sm="4">
                            <label for="input-numero">Remision</label>
                        </b-col> 
                        <b-col sm="8">
                            <b-form-input 
                                id="input-numero" 
                                type="number" 
                                v-model="num_remision" 
                                @keyup.enter="porNumero()">
                            </b-form-input>
                        </b-col>
                    </b-row>
                </b-col>
                <b-col sm="6">
                    <b-row class="my-1">
                        <b-col sm="2" align="right">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="10">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes()"></b-input>
                            <div class="list-group" v-if="resultsClientes.length" id="listaAD">
                                <a 
                                    href="#" 
                                    v-bind:key="i" 
                                    class="list-group-item list-group-item-action" 
                                    v-for="(result, i) in resultsClientes" 
                                    @click="adeudosCliente(result)">
                                    {{ result.name }}
                                </a>
                            </div>
                        </b-col>
                    </b-row>
                </b-col>
                <!-- <b-col sm="3" align="right">
                    <b-button variant="info" :disabled="loadRegisters" @click="obtenerAdeudos">
                        <b-spinner small v-if="loadRegisters"></b-spinner> {{ !loadRegisters ? 'Mostrar todo' : 'Cargando' }}
                    </b-button>
                </b-col> -->
            </b-row>
            <hr>
            <!-- LISTADO DE ADEUDOS -->
            <b-table 
                responsive
                :items="adeudos" 
                :fields="fieldsA" 
                :tbody-tr-class="rowClass"
                v-if="adeudos.length > 0"
                id="my-table" 
                :per-page="perPage" 
                :current-page="currentPage">
                <template slot="cliente_id" slot-scope="row">
                    {{ row.item.cliente.name }}
                </template>
                <template slot="total_devolucion" slot-scope="row">
                    ${{ row.item.total_devolucion | formatNumber }}
                </template>
                <template slot="total_adeudo" slot-scope="row">
                    ${{ row.item.total_adeudo | formatNumber }}
                </template>
                <template slot="total_abonos" slot-scope="row">
                    ${{ row.item.total_abonos | formatNumber }}
                </template>
                <template slot="total_pendiente" slot-scope="row">
                    ${{ row.item.total_pendiente | formatNumber }}
                </template>
                <template slot="detalles" slot-scope="row">
                    <b-button variant="info" @click="detallesAdeudo(row.item)">Detalles</b-button>
                </template>
                <template slot="registrar_pago" slot-scope="row">
                    <b-button 
                        v-if="row.item.total_pendiente != 0 && row.item.total_devolucion == 0" 
                        @click="registrarDevAdeudo(row.item, row.index)"
                        variant="primary">Registrar devolución
                    </b-button>
                </template>
                <template slot="thead-top" slot-scope="row">
                    <tr>
                        <th colspan="2"></th>
                        <th>${{ total_adeudo | formatNumber }}</th>
                        <th>${{ total_devolucion | formatNumber }}</th>
                        <th>${{ total_pagos | formatNumber }}</th>
                        <th>${{ total_pendiente | formatNumber }}</th>
                    </tr>
                </template>
            </b-table>
            <!-- PAGINACIÓN -->
            <b-pagination
                v-model="currentPage"
                :total-rows="adeudos.length"
                :per-page="perPage"
                aria-controls="my-table"
                v-if="adeudos.length > 0"
            ></b-pagination>
        </div>
        <!-- MOSTRAR DETALLES DEL ADEUDO -->
        <div v-if="mostrarAbonos">
            <b-row>
                <b-col sm="4">
                    <label><b>Remision No.</b>: {{ adeudo.remision_num }}</label><br>
                    <label><b>Fecha</b>: {{ adeudo.created_at | moment}}</label>
                </b-col>
                <b-col sm="6"  class="text-right">
                    <label><b>Total pendiente</b> ${{ adeudo.total_pendiente | formatNumber }}</label>
                </b-col>
                <b-col sm="2" align="right">
                    <b-button variant="secondary" @click="listadoAdeudos = true; mostrarAbonos = false;">
                        <i class="fa fa-mail-reply"></i> Regresar
                    </b-button>
                </b-col>
            </b-row>
            <label><b>Cliente</b>: {{ adeudo.cliente.name }}</label>
            <hr>
            <!-- SALIDA -->
            <div class="row" v-if="adeudo.datos.length > 0">
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
                <b-table :items="adeudo.datos" :fields="fieldsD">
                    <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                    <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.costo_unitario | formatNumber }}</template>
                    <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                    <!-- ENCABEZADO DE TOTALES -->
                    <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th colspan="5"></th>
                            <th>${{ adeudo.total_adeudo | formatNumber }}</th>
                        </tr>
                    </template>
                </b-table>
            </b-collapse>
            <hr>
            <!-- PAGOS -->
            <div class="row" v-if="adeudo.abonos.length > 0">
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
                <b-table :items="adeudo.abonos" :fields="fieldsP">
                    <template slot="index" slot-scope="row">
                        {{ row.index + 1 }}
                    </template>
                    <template slot="pago" slot-scope="row">
                        ${{ row.item.pago | formatNumber }}
                    </template>
                    <template slot="created_at" slot-scope="row">
                        {{ row.item.created_at | moment }}
                    </template>
                    <!-- ENCABEZADO DE TOTALES -->
                    <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th colspan="1"></th>
                            <th>${{ adeudo.total_abonos | formatNumber }}</th>
                        </tr>
                    </template>
                </b-table>
            </b-collapse>
            <hr>
            <div class="row" v-if="adeudo.devoluciones.length > 0">
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
                <b-table :items="adeudo.devoluciones" :fields="fieldsD">
                    <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                    <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                    <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                    <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario | formatNumber }}</template>
                    <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
                    <!-- ENCABEZADO DE TOTALES -->
                    <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th colspan="5"></th>
                            <th>${{ adeudo.total_devolucion | formatNumber }}</th>
                        </tr>
                    </template>
                </b-table>
            </b-collapse>
        </div>
        <!-- REGISTRAR DEVOLUCIÓN -->
        <div v-if="mostrarRegistrar">
            <h4 style="color: #170057">Registro de devolución</h4>
            <hr>
            <div class="row">
                <div class="col-md-6"><h5><b>Remisión No. {{ adeudo.remision_num }}</b></h5></div>
                <div class="col-md-3 text-right">
                    <b-button 
                        variant="success" 
                        @click="guardarDevolucion()" 
                        :disabled="load">
                        <i class="fa fa-check"></i> {{ !load ? 'Guardar' : 'Guardando' }} <b-spinner small v-if="load"></b-spinner>
                    </b-button>
                </div>
                <div class="col-md-3 text-right">
                    <b-button variant="secondary" @click="mostrarRegistrar = false; listadoAdeudos = true;"><i class="fa fa-mail-reply"></i> Regresar</b-button>
                </div>
            </div>
            <label><b>Cliente:</b> {{ adeudo.cliente.name }}</label>
            <hr>
            <b-table :items="adeudo.devoluciones" :fields="fieldsRD">
                <template slot="index" slot-scope="row">{{ row.index + 1 }}</template>
                <template slot="ISBN" slot-scope="row">{{ row.item.libro.ISBN }}</template>
                <template slot="titulo" slot-scope="row">{{ row.item.libro.titulo }}</template>
                <template slot="costo_unitario" slot-scope="row">${{ row.item.dato.costo_unitario | formatNumber }}</template>
                <template slot="unidades" slot-scope="row">
                    <b-input 
                        :id="`inpAdeuDev-${row.index}`"
                        type="number" 
                        @change="obtenerSubtotal(row.item, row.index)"
                        v-model="row.item.unidades">
                    </b-input>
                </template>
                <template slot="total" slot-scope="row">${{ row.item.total | formatNumber }}</template>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['role_id', 'registersall'],
        data() {
            return {
                datos: [],
                fieldsD: [
                    {key: 'index', label: 'N.'},
                    'ISBN',
                    {key: 'titulo', label: 'Libro'},
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'},
                    {key: 'eliminar', label: ''}
                ],
                fieldsRD: [
                    {key: 'index', label: 'N.'},
                    'ISBN',
                    {key: 'titulo', label: 'Libro'},
                    {key: 'costo_unitario', label: 'Costo unitario'},
                    {key: 'unidades_resta', label: 'Unidades pendientes'},
                    'unidades',
                    {key: 'total', label: 'Subtotal'}
                ],
                listadoAdeudos: true,
                mostrarRegistrar: false,
                mostrarSeleccionar: true,
                mostrarAbonos: false,
                mostrarForm: false,
                mostrarDatos: true,
                clientes: [],
                fieldsC: [
                    {key: 'name', label: 'Nombre'},
                    {key: 'email', label: 'Correo'},
                    {key: 'direccion', label: 'Dirección'},
                    {key: 'seleccionar', label: ''}
                ],
                cliente: {},
                adeudo: {
                    id: 0,
                    cliente_id: 0,
                    cliente: {},
                    remision_num: 0,
                    fecha_adeudo: '',
                    total_adeudo: 0,
                    total_pendiente: 0,
                    total_abonos: 0,
                    total_devolucion: 0,
                    datos: [],
                    devoluciones: [],
                    abonos: []
                },
                state: null,
                load: false,
                adeudos: this.registersall,
                fieldsA: [
                    {key: 'remision_num', label: 'Remisión No.'},
                    {key: 'cliente_id', label: 'Cliente'},
                    {key: 'total_adeudo', label: 'Total adeudo'},
                    {key: 'total_devolucion', label: 'Devolución'},
                    {key: 'total_abonos', label: 'Pagos'},
                    {key: 'total_pendiente', label: 'Total pendiente'},
                    {key: 'detalles', label: ''},
                    {key: 'registrar_pago', label: ''}
                ],
                fieldsP: [
                    {key: 'index', label: 'No.'},
                    'pago',
                    {key: 'created_at', label: 'Fecha de pago'},
                ],
                abono: {
                    adeudo_id: 0,
                    pago: 0,
                },
                posicion: null,
                queryCliente: '',
                resultsClientes: [],
                total_adeudo: 0,
                total_pagos: 0,
                total_pendiente: 0,
                total_devolucion: 0,
                stateR: null,
                isbn: '',
                inputISBN: true,
                temporal: {},
                queryTitulo: '',
                inputLibro: true,
                resultslibros: [],
                inputUnidades: false,
                unidades: '',
                costo_unitario: '',
                inputCosto: false,
                mostrarSalida: false,
                mostrarDevolucion: false,
                mostrarPagos: false,
                mostrarFinal: false,
                devoluciones: [],
                vendidos: [],
                num_remision: null,
                perPage: 15,
                currentPage: 1,
                loadRegisters: false
            }
        },
        filters: {
            moment: function (date) {
                return moment(date).format('DD-MM-YYYY');
            },
            formatNumber: function (value) {
                return numeral(value).format("0,0[.]00"); 
            }
        },
        created: function(){
			this.acumular();
        },
        methods: {
            // BUSCAR ADEUDO POR NUMERO DE REMISIÓN
            porNumero(){
                if(this.num_remision > 0){
                    axios.get('/buscar_adeudo', {params: {num_remision: this.num_remision}}).then(response => {
                        if(response.data.id != undefined){
                            this.adeudos = [];
                            this.adeudos.push(response.data);
                            this.acumular();
                        }
                        else{
                            this.makeToast('warning', 'Numero de remisión incorrecto');
                        }
                    }).catch(error => {
                        this.makeToast('danger', 'Ocurrio un problema, vuelve a intentarlo');
                    });
                }
            },
            // MOSTRAR COINCIDENCIA DE CLIENTES
            mostrarClientes(){
                if(this.queryCliente.length > 0){
                    axios.get('/mostrarClientes', {params: {queryCliente: this.queryCliente}}).then(response => {
                        this.resultsClientes = response.data;
                    }); 
                }
                else{
                    this.resultsClientes = [];
                }
            },
            // MOSTRAR ADEUDOS DEL CLIENTE SELECCIONADO
            adeudosCliente(cliente){
                this.resultsClientes = [];
                this.queryCliente = cliente.name;
                axios.get('/adeudos_cliente', {params: {cliente_id: cliente.id}}).then(response => {
                    this.adeudos = response.data;
                    this.acumular();
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // MOSTRAR LOS DETALLES DE ADEUDO
            detallesAdeudo(adeudo){
                this.ini_adeudo();
                axios.get('/detalles_adeudo', {params: {id: adeudo.id}}).then(response => {
                    this.mostrarSalida = false;
                    this.mostrarDevolucion = false;
                    this.mostrarPagos = false;
                    this.mostrarFinal = false;
                    this.adeudo = {
                        cliente_id: response.data.adeudo.cliente_id,
                        cliente: response.data.adeudo.cliente,
                        remision_num: response.data.adeudo.remision_num,
                        fecha_adeudo: response.data.adeudo.fecha_adeudo,
                        total_adeudo: response.data.adeudo.total_adeudo,
                        total_abonos: response.data.adeudo.total_abonos,
                        total_devolucion: response.data.adeudo.total_devolucion,
                        total_pendiente: response.data.adeudo.total_pendiente,
                        datos: response.data.datos,
                        devoluciones: response.data.devoluciones,
                        abonos: response.data.adeudo.abonos
                    };
                    this.listadoAdeudos = false;
                    this.mostrarAbonos = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // REGISTRAR DEVOLUCIÓN DE ADEUDO
            registrarDevAdeudo(adeudo, i){
                this.posicion = i;
                this.ini_adeudo();
                axios.get('/detalles_adeudo', {params: {id: adeudo.id}}).then(response => {
                    this.adeudo = {
                        id: response.data.adeudo.id,
                        cliente_id: response.data.adeudo.cliente_id,
                        cliente: response.data.adeudo.cliente,
                        remision_num: response.data.adeudo.remision_num,
                        fecha_adeudo: response.data.adeudo.fecha_adeudo,
                        total_adeudo: response.data.adeudo.total_adeudo,
                        total_abonos: response.data.adeudo.total_abonos,
                        total_devolucion: response.data.adeudo.total_devolucion,
                        total_pendiente: response.data.adeudo.total_pendiente,
                        datos: response.data.datos,
                        devoluciones: response.data.devoluciones
                    };
                    this.listadoAdeudos = false;
                    this.mostrarRegistrar = true;
                }).catch(error => {
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // GUARDAR DEVOLUCIÓN
            guardarDevolucion(){
                this.load = true;
                axios.put('/guardar_adeudo_devolucion', this.adeudo).then(response => {
                    this.adeudos[this.posicion].total_pendiente = response.data.total_pendiente;
                    this.adeudos[this.posicion].total_devolucion = response.data.total_devolucion;
                    this.acumular();
                    this.load = false;
                    this.mostrarRegistrar = false;
                    this.listadoAdeudos = true;
                }).catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrió un problema. Verifica tu conexión a internet y/o vuelve a intentar.');
                });
            },
            // VERIFICAR LAS UNIDADES PARA OBTENER EL SUBTOTAL
            obtenerSubtotal(devolucion, i){
                if(devolucion.unidades >= 0){
                    if(devolucion.unidades > devolucion.unidades_resta){
                        this.makeToast('warning', 'Las unidades son mayor a las unidades pendientes');
                        this.adeudo.devoluciones[i].unidades = 0;
                        this.adeudo.devoluciones[i].total = 0;
                    }
                    else{
                        this.adeudo.devoluciones[i].total = devolucion.unidades * devolucion.dato.costo_unitario;
                        if(i + 1 < this.adeudo.devoluciones.length){
                            document.getElementById('inpAdeuDev-'+(i+1)).focus();
                            document.getElementById('inpAdeuDev-'+(i+1)).select();
                        }
                    }
                }
                else{
                    this.makeToast('warning', 'Unidades invalidas');
                }
            },
            rowClass(item, type) {
                if (!item) return
                if (item.total_pendiente == 0) return 'table-success'
            },
            ini_adeudo(){
                this.adeudo = {
                    id: 0,
                    cliente_id: 0,
                    cliente: {},
                    remision_num: 0,
                    fecha_adeudo: '',
                    total_adeudo: 0,
                    total_pendiente: 0,
                    total_abonos: 0,
                    total_devolucion: 0,
                    datos: [],
                    devoluciones: [],
                    abonos: []
                };
            },
            acumular(){
                this.total_adeudo = 0;
                this.total_pagos = 0;
                this.total_pendiente = 0;
                this.total_devolucion = 0;
                this.adeudos.forEach(adeudo => {
                    this.total_adeudo += adeudo.total_adeudo;
                    this.total_pagos += adeudo.total_abonos;
                    this.total_pendiente += adeudo.total_pendiente;
                    this.total_devolucion += adeudo.total_devolucion;
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
    #btnEditar {
        color: #ffb300;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
    #btnCancelar {
        color: red;
        background-color: transparent;
        border: 0ch;
        font-size: 25px;
    }
    #txtObligatorio {
        color: red;
    }
    #listaAD{
        position: absolute;
        z-index: 100
    }
</style>
