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
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-cliente">Cliente</label>
                        </b-col>
                        <b-col sm="9">
                            <b-input v-model="queryCliente" @keyup="mostrarClientes"
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
                            <label for="input-inicio">De:</label>
                        </b-col>
                        <b-col sm="9">
                            <input 
                                class="form-control" 
                                type="date" 
                                v-model="inicio">
                            </input>
                            <div class="text-danger">{{ respuesta_fecha }}</div>
                        </b-col>
                    </b-row>
                    <b-row class="my-1">
                        <b-col sm="3">
                            <label for="input-final">A: </label>
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
                <b-table 
                    :items="remisiones" 
                    :fields="fields" 
                    v-if="remisiones.length"
                    :tbody-tr-class="rowClass">
                    <template slot="cliente_id" slot-scope="row">
                        {{ row.item.cliente.name }}
                    </template>
                    <template slot="total" slot-scope="row">
                        ${{ row.item.total }}
                    </template>
                    <template slot="total_devolucion" slot-scope="row">
                        ${{ row.item.total_devolucion }}
                    </template>
                    <template slot="pagos" slot-scope="row">
                        ${{ row.item.pagos }}
                    </template>
                    <template slot="total_pagar" slot-scope="row">
                        ${{ row.item.total_pagar }}
                    </template>
                    <template slot="detalles" slot-scope="row">
                        <b-button 
                            variant="info"
                            @click="detallesRemision(row.item)">
                            Detalles
                        </b-button>
                    </template>
                    <template slot="thead-top" slot-scope="row">
                        <tr>
                            <th colspan="3"></th>
                            <th>${{ total_salida }}</th>
                            <th>${{ total_devolucion }}</th>
                            <th>${{ total_pagos }}</th>
                            <th>${{ total_pagar }}</th>
                        </tr>
                    </template>
                </b-table>
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
                        v-if="remision.estado == 'Iniciado' && role_id == 2">
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
                <b-button :disabled="load" @click="cambiarEstado">OK</b-button>
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
                fields: [
                    { key: 'id', label: 'Folio' },
                    { key: 'fecha_creacion', label: 'Fecha de creación' },
                    { key: 'cliente_id', label: 'Cliente' },
                    { key: 'total', label: 'Salida' },
                    { key: 'total_devolucion', label: 'Devolución' },
                    'pagos',
                    { key: 'total_pagar', label: 'Pagar' },
                    { key: 'detalles', label: '' }
                ],
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
                    { value: 'Proceso', text: 'Entregado' },
                    { value: 'Iniciado', text: 'Iniciado' },
                ],
                imprimirCliente: false,
                imprimirEstado:false,
                estadoRemision: '',
                queryTitulo: '',
                resultslibros: [],
                tabla_libros: false,
                libros: [],
                mostrar: true,
                show: false,
                mostrarDatos: false,
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
                total_remision: 0,
                pagar: 0,
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
                load: false,
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
            rowClass(item, type) {
                if (!item) return
                if (item.estado == 'Iniciado') return 'table-secondary'
                if (item.estado == 'Cancelado') return 'table-danger'
                if (item.total_pagar == 0 && item.pagos > 0) return 'table-success'
            },
            porNumero(){
                if(this.num_remision > 0){
                    this.respuesta_numero = '';
                    axios.get('/buscar_por_numero', {params: {num_remision: this.num_remision}}).then(response => {
                        this.remision = response.data.remision;
                        this.remisiones = [];
                        this.remisiones.push(this.remision);
                        this.acumular();
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
                    this.queryCliente = result.name;
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
                this.tabla_gral = true;
                this.acumular();
            },
            cambiarEstado(){
                this.load = true;
                axios.put('/cancelar_remision', this.remision).then(response => {
                    this.remision.estado = response.data.estado;
                    this.$bvModal.hide('modal-cancelar');
                    this.load = false;
                    this.makeToast('secondary', 'Remisión cancelada');
                })
                .catch(error => {
                    this.load = false;
                    this.makeToast('danger', 'Ocurrio un problema, vuelve a intentar o actualiza la pagina');
                });
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